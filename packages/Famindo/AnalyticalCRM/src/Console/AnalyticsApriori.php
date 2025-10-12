<?php

namespace Famindo\AnalyticalCRM\Console;

use Carbon\Carbon;
use Famindo\AnalyticalCRM\Services\TransactionETL;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Phpml\Association\Apriori;

class AnalyticsApriori extends Command
{
    protected $signature = 'analytics:apriori
        {--from= : Start date (Y-m-d)}
        {--to= : End date (Y-m-d)}
        {--support=0.05 : Minimum support (0..1)}
        {--confidence=0.6 : Minimum confidence (0..1)}
        {--min-items=1 : Minimum items per transaction}
        {--persist : Persist transactions to apriori_transactions}
        {--save : Persist generated rules to apriori_rules}
        {--customer_ids=* : Filter by customer IDs}
        {--organization_ids=* : Filter by organization IDs}
        {--created_by= : User ID to set on rules}
        {--limit=0 : Max rules to save (0 = all)}
        {--dry-run : Do not persist anything, just show summary}
    ';

    protected $description = 'Build transactions via ETL, train Apriori, and (optionally) save rules';

    public function handle(TransactionETL $etl)
    {
        $fromOpt = $this->option('from');
        $toOpt = $this->option('to');

        $from = $fromOpt ? Carbon::parse($fromOpt) : Carbon::now()->subDays(90)->startOfDay();
        $to = $toOpt ? Carbon::parse($toOpt) : Carbon::now()->endOfDay();

        $support = (float) $this->option('support');
        $confidence = (float) $this->option('confidence');
        $minItems = (int) $this->option('min-items');
        $persistTx = (bool) $this->option('persist');
        $saveRules = (bool) $this->option('save');
        $dryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $createdBy = $this->option('created_by');
        $customerIds = (array) $this->option('customer_ids');
        $organizationIds = (array) $this->option('organization_ids');

        $this->info(sprintf('Building transactions from %s to %s ...', $from->toDateString(), $to->toDateString()));

        $transactions = $etl->build([
            'from'             => $from,
            'to'               => $to,
            'customer_ids'     => $customerIds,
            'organization_ids' => $organizationIds,
            'min_items'        => $minItems,
            'persist'          => $persistTx && ! $dryRun,
        ]);

        $countTx = count($transactions);
        $this->info("Transactions: $countTx");

        if ($countTx === 0) {
            $this->warn('No transactions found for selected filters.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Training Apriori (support=%.4f, confidence=%.4f)...', $support, $confidence));

        $assoc = new Apriori($support, $confidence);
        $assoc->train($transactions, []);

        $rules = $assoc->getRules();
        $countRules = count($rules);
        $this->info("Generated rules: $countRules");

        if ($countRules === 0) {
            $this->warn('No rules generated. Consider lowering thresholds.');
        }

        // Compute lift and sort
        $withLift = [];
        foreach ($rules as $rule) {
            $lhs = Arr::get($rule, Apriori::ARRAY_KEY_ANTECEDENT, []);
            $rhs = Arr::get($rule, Apriori::ARRAY_KEY_CONSEQUENT, []);
            $suppXY = (float) Arr::get($rule, Apriori::ARRAY_KEY_SUPPORT, 0.0);
            $conf = (float) Arr::get($rule, Apriori::ARRAY_KEY_CONFIDENCE, 0.0);
            $suppY = $this->supportFractionOf($rhs, $transactions);
            $lift = $suppY > 0 ? ($conf / $suppY) : 0.0;

            $withLift[] = [
                'lhs'       => array_values($lhs),
                'rhs'       => array_values($rhs),
                'support'   => $suppXY,
                'confidence'=> $conf,
                'lift'      => $lift,
            ];
        }

        // Sort by lift desc, then confidence desc, then support desc
        usort($withLift, function ($a, $b) {
            return [$b['lift'], $b['confidence'], $b['support']] <=> [$a['lift'], $a['confidence'], $a['support']];
        });

        if ($limit > 0) {
            $withLift = array_slice($withLift, 0, $limit);
        }

        $this->line(sprintf('Top %d rules (by lift):', min(10, count($withLift))));
        foreach (array_slice($withLift, 0, 10) as $idx => $r) {
            $this->line(sprintf('%2d) [%s] => [%s] | supp=%.4f conf=%.4f lift=%.4f',
                $idx + 1,
                implode(', ', $r['lhs']),
                implode(', ', $r['rhs']),
                $r['support'],
                $r['confidence'],
                $r['lift']
            ));
        }

        if ($saveRules && ! $dryRun && ! empty($withLift)) {
            $this->persistRules($withLift, $from, $to, [
                'support'     => $support,
                'confidence'  => $confidence,
                'min_items'   => $minItems,
                'filters'     => [
                    'customer_ids'     => $customerIds,
                    'organization_ids' => $organizationIds,
                ],
            ], $createdBy);

            $this->info(sprintf('Saved %d rules to apriori_rules.', count($withLift)));
        }

        return self::SUCCESS;
    }

    private function supportFractionOf(array $subset, array $transactions): float
    {
        if (empty($subset)) {
            return 0.0;
        }

        $n = count($transactions);
        if ($n === 0) {
            return 0.0;
        }

        $count = 0;
        foreach ($transactions as $t) {
            if ($this->isSubset($subset, $t)) {
                $count++;
            }
        }

        return $count / $n;
    }

    private function isSubset(array $subset, array $set): bool
    {
        return count(array_diff($subset, array_intersect($subset, $set))) === 0;
    }

    private function persistRules(array $rules, Carbon $from, Carbon $to, array $params, $createdBy = null): void
    {
        $now = Carbon::now();
        $batch = [];
        foreach ($rules as $r) {
            $batch[] = [
                'lhs'          => json_encode($r['lhs'], JSON_UNESCAPED_UNICODE),
                'rhs'          => json_encode($r['rhs'], JSON_UNESCAPED_UNICODE),
                'support'      => $r['support'],
                'confidence'   => $r['confidence'],
                'lift'         => $r['lift'],
                'period_start' => $from->copy(),
                'period_end'   => $to->copy(),
                'params_json'  => json_encode($params, JSON_UNESCAPED_UNICODE),
                'created_by'   => $createdBy ? (int) $createdBy : null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        foreach (array_chunk($batch, 500) as $chunk) {
            DB::table('apriori_rules')->insert($chunk);
        }
    }
}
