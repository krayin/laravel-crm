<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Quote\Repositories\QuoteRepository;

class LeadQuoteDemoSeeder extends Seeder
{
    private const TAG = '[DEMO] AnalyticalCRM LeadQuote';

    public function run(): void
    {
        DB::transaction(function () {
            $quoteIds = DB::table('quotes')
                ->where('description', 'like', self::TAG.'%')
                ->pluck('id');

            if ($quoteIds->isNotEmpty()) {
                DB::table('lead_quotes')->whereIn('quote_id', $quoteIds)->delete();
                DB::table('quote_items')->whereIn('quote_id', $quoteIds)->delete();
                DB::table('quotes')->whereIn('id', $quoteIds)->delete();
            }

            $leadIds = DB::table('leads')
                ->where('description', 'like', self::TAG.'%')
                ->pluck('id');

            if ($leadIds->isNotEmpty()) {
                DB::table('lead_quotes')->whereIn('lead_id', $leadIds)->delete();
                DB::table('lead_products')->whereIn('lead_id', $leadIds)->delete();
                DB::table('lead_activities')->whereIn('lead_id', $leadIds)->delete();
                DB::table('leads')->whereIn('id', $leadIds)->delete();
            }
        });

        $adminId = $this->resolveDefaultOwner();
        $leadSourceIds = DB::table('lead_sources')->pluck('id')->all();
        $leadTypeIds = DB::table('lead_types')->pluck('id')->all();

        $pipelineId = DB::table('lead_pipelines')->where('is_default', 1)->value('id') ?? 1;
        $wonStageId = DB::table('lead_pipeline_stages')
            ->where('lead_pipeline_id', $pipelineId)
            ->where('code', 'won')
            ->value('id');

        if (! $wonStageId) {
            throw new \RuntimeException('Lead pipeline stage "won" not found.');
        }

        $products = DB::table('products')
            ->select('id', 'sku', 'name', 'price')
            ->get()
            ->keyBy('sku');

        $headSkus = [
            'FFL-DRY-CHAM',
            'FFL-DRY-HEAT',
            'FFL-PID-TC',
            'FFL-TC-K',
            'FFL-FR-SS304',
            'FFL-CNV-MOD',
            'FFL-CNV-DRV',
            'FFL-VFD-075',
            'FFL-SENS-PE',
            'ALV-TANK-PE',
            'ALV-PUMP-DIA',
            'ALV-PLC-STD',
            'ALV-PANEL-IP65',
            'TST-FRAME',
            'TST-ACT-LIN',
            'TST-PLC-IO',
            'BLK-FR-WELD',
            'BLK-PLC-IO',
            'BLK-ENC-IP54',
            'BLK-LCURT',
        ];

        $bundleConfigs = $this->buildBundleConfigs($products, $headSkus);

        $orgPlan = $this->buildOrganizationPlan();

        $organizations = DB::table('organizations')
            ->select('id', 'name', 'address')
            ->whereIn('name', array_column($orgPlan, 'name'))
            ->get()
            ->keyBy('name');

        $persons = DB::table('persons')
            ->select('id', 'organization_id')
            ->whereIn('organization_id', $organizations->pluck('id'))
            ->get()
            ->groupBy('organization_id')
            ->map(fn ($rows) => $rows->first()->id);

        $addressBook = $this->buildAddressBook($organizations);

        $longTailCounters = $this->buildLongTailCounters($bundleConfigs, $orgPlan);
        $longTailTotals = [];

        foreach ($longTailCounters as $bundleKey => $counter) {
            $longTailTotals[$bundleKey] = array_sum($counter);
        }

        $midCounters = $this->buildMidCounters($bundleConfigs, $orgPlan, $longTailTotals);

        /** @var LeadRepository $leadRepository */
        $leadRepository = app(LeadRepository::class);

        /** @var QuoteRepository $quoteRepository */
        $quoteRepository = app(QuoteRepository::class);

        $quoteIndex = 1;

        foreach ($orgPlan as $plan) {
            $organization = $organizations[$plan['name']] ?? null;

            if (! $organization) {
                continue;
            }

            $personId = $persons[$organization->id] ?? null;

            if (! $personId) {
                continue;
            }

            $bundleKey = $plan['bundle'];
            $bundle = $bundleConfigs[$bundleKey];

            for ($i = 0; $i < $plan['quotes']; $i++) {
                $quotesRemaining = $plan['quotes'] - $i;

                $longTailItem = $this->maybePickLongTail($bundleKey, $longTailCounters, $quotesRemaining);

                $midCount = $longTailItem ? 1 : 2;
                $midItems = $this->pickMidItems($bundleKey, $midCounters, $midCount);

                $skuList = array_merge(
                    $bundle['core_head'],
                    $midItems,
                    $longTailItem ? [$longTailItem] : []
                );

                $skuList = array_values(array_unique($skuList));

                $items = [];
                $subTotal = 0.0;

                foreach ($skuList as $sku) {
                    $product = $products[$sku] ?? null;

                    if (! $product) {
                        continue 2;
                    }

                    $price = round((float) $product->price, 4);

                    $items[] = [
                        'product_id'       => $product->id,
                        'sku'              => $product->sku,
                        'name'             => $product->name,
                        'quantity'         => 1,
                        'price'            => $price,
                        'total'            => $price,
                        'discount_percent' => 0,
                        'discount_amount'  => 0,
                        'tax_percent'      => 0,
                        'tax_amount'       => 0,
                    ];

                    $subTotal += $price;
                }

                if (empty($items)) {
                    continue;
                }

                $closeDate = $this->randomDateIn2025();
                $createdAt = (clone $closeDate)->subDays(random_int(20, 90))->setTime(random_int(8, 16), random_int(0, 59));
                $closedAt = (clone $closeDate)->setTime(random_int(10, 18), random_int(0, 59));

                $leadTitle = $bundle['label'].' untuk '.$plan['name'];
                $quoteSubject = 'Penawaran '.$bundle['label'].' untuk '.$plan['name'].' (Rev.1)';

                $leadData = [
                    'entity_type'            => 'leads',
                    'title'                  => $leadTitle,
                    'description'            => self::TAG.' '.$bundleKey.' #'.str_pad((string) $quoteIndex, 3, '0', STR_PAD_LEFT),
                    'lead_value'             => $this->computeLeadValue($items),
                    'status'                 => 1,
                    'expected_close_date'    => $closeDate->toDateString(),
                    'closed_at'              => $closedAt,
                    'user_id'                => $adminId,
                    'person_id'              => $personId,
                    'lead_source_id'         => Arr::random($leadSourceIds),
                    'lead_type_id'           => Arr::random($leadTypeIds),
                    'lead_pipeline_id'       => $pipelineId,
                    'lead_pipeline_stage_id' => $wonStageId,
                ];

                $lead = $leadRepository->create($leadData);

                DB::table('leads')->where('id', $lead->id)->update([
                    'created_at' => $createdAt,
                    'updated_at' => $closedAt,
                ]);

                $address = $addressBook[$plan['name']] ?? $this->fallbackAddress($plan['name']);

                $quoteData = [
                    'entity_type'      => 'quotes',
                    'subject'          => $quoteSubject,
                    'description'      => self::TAG.' '.$bundleKey.' #'.str_pad((string) $quoteIndex, 3, '0', STR_PAD_LEFT),
                    'billing_address'  => $address,
                    'shipping_address' => $address,
                    'discount_percent' => 0,
                    'discount_amount'  => 0,
                    'tax_amount'       => 0,
                    'adjustment_amount'=> 0,
                    'sub_total'        => round($subTotal, 4),
                    'grand_total'      => round($subTotal, 4),
                    'expired_at'       => $closeDate,
                    'user_id'          => $adminId,
                    'person_id'        => $personId,
                    'items'            => $items,
                ];

                $quote = $quoteRepository->create($quoteData);

                DB::table('quotes')->where('id', $quote->id)->update([
                    'created_at' => $createdAt,
                    'updated_at' => $closedAt,
                ]);

                DB::table('lead_quotes')->insert([
                    'lead_id'  => $lead->id,
                    'quote_id' => $quote->id,
                ]);

                $quoteIndex++;
            }
        }
    }

    private function resolveDefaultOwner(): int
    {
        $adminId = DB::table('users')
            ->where('email', 'admin@example.com')
            ->value('id');

        if ($adminId) {
            return (int) $adminId;
        }

        return (int) DB::table('users')->min('id');
    }

    private function buildBundleConfigs($products, array $headSkus): array
    {
        foreach ($headSkus as $sku) {
            if (! isset($products[$sku])) {
                throw new \RuntimeException(sprintf('Head product SKU "%s" not found in catalog.', $sku));
            }
        }

        $bundleConfigs = [
            'oven' => [
                'label'       => 'Sistem Oven Pengering Modular',
                'core_head'   => ['FFL-DRY-CHAM', 'FFL-DRY-HEAT', 'FFL-PID-TC', 'FFL-TC-K'],
                'mid'         => [
                    'FFL-INS-50',
                    'FFL-HUM-SNS',
                    'FFL-DRAIN-SET',
                    'FFL-NOZ-CIP',
                    'FFL-SENS-PRX',
                    'FFL-GUARD-SS',
                    'FFL-ESTOP',
                    'FFL-SKN-UNIT',
                ],
                'long_tail_prefix' => 'FFL',
            ],
            'conveyor' => [
                'label'       => 'Sistem Konveyor Stainless Modular',
                'core_head'   => ['FFL-FR-SS304', 'FFL-CNV-MOD', 'FFL-CNV-DRV', 'FFL-VFD-075', 'FFL-SENS-PE'],
                'mid'         => [
                    'FFL-PNU-CLAMP',
                    'FFL-VLV-52',
                    'FFL-FRL-14',
                    'FFL-FIT-PT',
                    'FFL-HMI-7',
                    'FFL-BIN-SS304',
                    'BLK-SENS-REED',
                    'BLK-PRS-SNS',
                ],
                'long_tail_prefix' => 'FFL',
            ],
            'coating' => [
                'label'       => 'Sistem Coating & Dosing Cairan',
                'core_head'   => ['ALV-TANK-PE', 'ALV-PUMP-DIA', 'ALV-PLC-STD', 'ALV-PANEL-IP65'],
                'mid'         => [
                    'ALV-PUMP-PR',
                    'ALV-CHK-VLV',
                    'ALV-NEEDLE',
                    'ALV-FILTER-IN',
                    'ALV-TUBE-PTFE',
                    'ALV-QC-FIT',
                    'ALV-DLOG',
                    'ALV-TWR-LGT',
                ],
                'long_tail_prefix' => 'ALV',
            ],
            'testing' => [
                'label'       => 'Rig Pengujian & Kalibrasi',
                'core_head'   => ['TST-FRAME', 'TST-ACT-LIN', 'TST-PLC-IO'],
                'mid'         => [
                    'TST-DRV-CTRL',
                    'TST-LVDT',
                    'TST-LIMIT',
                    'TST-PS-24V',
                    'TST-SAF-DOOR',
                    'TST-HMI-7',
                ],
                'long_tail_prefix' => 'TST',
            ],
            'press' => [
                'label'       => 'Jig Press & Automation Pneumatik',
                'core_head'   => ['BLK-FR-WELD', 'BLK-PLC-IO', 'BLK-ENC-IP54', 'BLK-LCURT'],
                'mid'         => [
                    'BLK-STRIPR',
                    'BLK-CLAMP-TG',
                    'BLK-LIN-GUIDE',
                    'BLK-SILENCER',
                    'BLK-FLOW',
                    'BLK-FRL-38',
                ],
                'long_tail_prefix' => 'BLK',
            ],
        ];

        $allSkus = $products->keys()->all();
        $midSkus = array_merge(
            $bundleConfigs['oven']['mid'],
            $bundleConfigs['conveyor']['mid'],
            $bundleConfigs['coating']['mid'],
            $bundleConfigs['testing']['mid'],
            $bundleConfigs['press']['mid'],
        );

        $unusedSkus = array_values(array_diff($allSkus, $headSkus, $midSkus));

        $grouped = [];

        foreach ($unusedSkus as $sku) {
            $prefix = strtok($sku, '-');
            $grouped[$prefix][] = $sku;
        }

        foreach ($bundleConfigs as $key => &$config) {
            $prefix = $config['long_tail_prefix'];
            $config['long_tail'] = $grouped[$prefix] ?? [];
        }

        return $bundleConfigs;
    }

    private function buildOrganizationPlan(): array
    {
        return [
            ['name' => 'PT Andalas Food',   'bundle' => 'oven',     'quotes' => 24],
            ['name' => 'PT Cipta Rasa',     'bundle' => 'oven',     'quotes' => 23],
            ['name' => 'PT Surya Bakery',   'bundle' => 'oven',     'quotes' => 23],
            ['name' => 'PT Nusantara Steel','bundle' => 'conveyor', 'quotes' => 24],
            ['name' => 'PT Prima Plastik',  'bundle' => 'conveyor', 'quotes' => 23],
            ['name' => 'PT Maju Jaya',      'bundle' => 'conveyor', 'quotes' => 23],
            ['name' => 'PT Arjuna Metal',   'bundle' => 'coating',  'quotes' => 30],
            ['name' => 'PT Barokah Logam',  'bundle' => 'coating',  'quotes' => 30],
            ['name' => 'PT Sejahtera Abadi','bundle' => 'testing',  'quotes' => 25],
            ['name' => 'CV Sentosa',        'bundle' => 'testing',  'quotes' => 25],
            ['name' => 'PT Delta Pharma',   'bundle' => 'press',    'quotes' => 25],
            ['name' => 'PT Sinar Elektrik', 'bundle' => 'press',    'quotes' => 25],
        ];
    }

    private function buildAddressBook($organizations): array
    {
        $stateMap = [
            'Padang'    => ['state' => 'Sumatera Barat', 'postcode' => '25112'],
            'Jakarta'   => ['state' => 'DKI Jakarta',    'postcode' => '10210'],
            'Surabaya'  => ['state' => 'Jawa Timur',     'postcode' => '60111'],
            'Bekasi'    => ['state' => 'Jawa Barat',     'postcode' => '17113'],
            'Semarang'  => ['state' => 'Jawa Tengah',    'postcode' => '50242'],
            'Bandung'   => ['state' => 'Jawa Barat',     'postcode' => '40115'],
            'Tangerang' => ['state' => 'Banten',         'postcode' => '15111'],
            'Depok'     => ['state' => 'Jawa Barat',     'postcode' => '16432'],
            'Gresik'    => ['state' => 'Jawa Timur',     'postcode' => '61125'],
            'Sidoarjo'  => ['state' => 'Jawa Timur',     'postcode' => '61215'],
            'Cikarang'  => ['state' => 'Jawa Barat',     'postcode' => '17530'],
            'Karawang'  => ['state' => 'Jawa Barat',     'postcode' => '41315'],
        ];

        $addresses = [];

        foreach ($organizations as $organization) {
            $meta = json_decode($organization->address ?? '{}', true) ?? [];
            $city = $meta['city'] ?? 'Jakarta';
            $industry = $meta['industry'] ?? 'Manufacturing';
            $code = $meta['code'] ?? 'C000';

            $region = $stateMap[$city] ?? ['state' => 'Jawa Barat', 'postcode' => '40111'];

            $addresses[$organization->name] = [
                'company'  => $organization->name,
                'address1' => 'Jl. Industri '.$code.' '.$industry,
                'city'     => $city,
                'state'    => $region['state'],
                'country'  => 'ID',
                'postcode' => $region['postcode'],
            ];
        }

        return $addresses;
    }

    private function fallbackAddress(string $name): array
    {
        return [
            'company'  => $name,
            'address1' => 'Jl. Industri No. 1',
            'city'     => 'Jakarta',
            'state'    => 'DKI Jakarta',
            'country'  => 'ID',
            'postcode' => '10210',
        ];
    }

    private function buildMidCounters(array $bundleConfigs, array $orgPlan, array $longTailTotals): array
    {
        $counters = [];

        foreach ($bundleConfigs as $key => $bundle) {
            $quotes = $this->quotesByBundle($orgPlan, $key);
            $skuList = $bundle['mid'];
            $count = count($skuList);

            if ($count === 0 || $quotes === 0) {
                $counters[$key] = [];

                continue;
            }

            $totalLongTail = $longTailTotals[$key] ?? 0;
            $totalNeeded = $quotes * 2 - $totalLongTail;
            $minimumRequired = $count * 15;

            $totalNeeded = max($totalNeeded, $minimumRequired);
            $totalNeeded = min($totalNeeded, $quotes * 2);

            $base = intdiv($totalNeeded, $count);
            $remainder = $totalNeeded % $count;

            $base = max(15, $base);

            $counters[$key] = array_fill_keys($skuList, $base);

            $availableKeys = array_keys($counters[$key]);

            while ($remainder > 0) {
                $sku = Arr::random($availableKeys);
                $counters[$key][$sku]++;
                $remainder--;
            }
        }

        return $counters;
    }

    private function buildLongTailCounters(array $bundleConfigs, array $orgPlan): array
    {
        $counters = [];

        foreach ($bundleConfigs as $key => $bundle) {
            $quotes = $this->quotesByBundle($orgPlan, $key);
            $skuList = $bundle['long_tail'];
            $count = count($skuList);
            $midCount = count($bundleConfigs[$key]['mid']);

            if ($count === 0 || $quotes === 0) {
                $counters[$key] = [];

                continue;
            }

            $target = (int) round($quotes * 0.55);
            $target = max($target, $count * 3);
            $target = min($target, $quotes);

            $maxLongTail = max(0, $quotes * 2 - ($midCount * 15));

            if ($maxLongTail <= 0) {
                $counters[$key] = [];

                continue;
            }

            $target = min($target, $maxLongTail);

            $maxCandidates = max(1, intdiv($maxLongTail, 3));
            $skuList = array_slice($skuList, 0, min($count, $maxCandidates));
            $count = count($skuList);

            if ($count === 0 || $target === 0) {
                $counters[$key] = [];

                continue;
            }

            $target = max($target, $count * 3);
            $target = min($target, $maxLongTail);

            $base = max(3, intdiv($target, $count));
            $remainder = $target - ($base * $count);

            $counters[$key] = array_fill_keys($skuList, $base);

            $availableKeys = array_keys($counters[$key]);

            while ($remainder > 0) {
                $sku = Arr::random($availableKeys);
                $counters[$key][$sku]++;
                $remainder--;
            }
        }

        return $counters;
    }

    private function quotesByBundle(array $orgPlan, string $bundleKey): int
    {
        return array_reduce($orgPlan, function ($carry, $item) use ($bundleKey) {
            if ($item['bundle'] === $bundleKey) {
                return $carry + $item['quotes'];
            }

            return $carry;
        }, 0);
    }

    private function pickMidItems(string $bundleKey, array &$midCounters, int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        if (empty($midCounters[$bundleKey])) {
            return [];
        }

        $selected = [];

        for ($i = 0; $i < $count; $i++) {
            $choices = array_filter($midCounters[$bundleKey], fn ($remaining) => $remaining > 0);

            if (empty($choices)) {
                break;
            }

            $choices = array_diff_key($choices, array_flip($selected));

            if (empty($choices)) {
                $choices = array_filter($midCounters[$bundleKey], fn ($remaining) => $remaining > 0);
            }

            $sku = Arr::random(array_keys($choices));

            $midCounters[$bundleKey][$sku]--;

            $selected[] = $sku;
        }

        return $selected;
    }

    private function maybePickLongTail(string $bundleKey, array &$longTailCounters, int $quotesRemaining): ?string
    {
        if (empty($longTailCounters[$bundleKey])) {
            return null;
        }

        $totalRemaining = array_sum($longTailCounters[$bundleKey]);

        if ($totalRemaining === 0 || $quotesRemaining <= 0) {
            return null;
        }

        $probability = min(1, $totalRemaining / $quotesRemaining);

        if (mt_rand() / mt_getrandmax() > $probability) {
            return null;
        }

        $choices = array_filter($longTailCounters[$bundleKey], fn ($remaining) => $remaining > 0);

        if (empty($choices)) {
            return null;
        }

        $sku = Arr::random(array_keys($choices));
        $longTailCounters[$bundleKey][$sku]--;

        return $sku;
    }

    private function randomDateIn2025(): Carbon
    {
        $start = Carbon::create(2025, 1, 1, 10, 0, 0);

        return (clone $start)->addDays(random_int(0, 364));
    }

    private function computeLeadValue(array $items): float
    {
        $sum = array_reduce($items, fn ($carry, $item) => $carry + ($item['total'] ?? 0), 0.0);

        $factor = random_int(105, 130) / 100;
        $value = $sum * $factor;

        return round($value / 10000) * 10000;
    }
}
