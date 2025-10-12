<?php

namespace Famindo\AnalyticalCRM\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TransactionETL
{
    /**
     * Build market-basket transactions from engineering orders + items.
     *
     * Options (all optional):
     * - from: Y-m-d or Carbon
     * - to: Y-m-d or Carbon
     * - customer_ids: int[]
     * - organization_ids: int[]
     * - min_items: int (remove transactions with fewer items)
     * - persist: bool (save to apriori_transactions)
     *
     * Returns array of transactions: [["itemA","itemB"], ["itemC"], ...]
     */
    public function build(array $options = []): array
    {
        $from = $this->toCarbonOrNull(Arr::get($options, 'from'));
        $to = $this->toCarbonOrNull(Arr::get($options, 'to'));
        $customerIds = Arr::get($options, 'customer_ids');
        $organizationIds = Arr::get($options, 'organization_ids');
        $minItems = (int) (Arr::get($options, 'min_items', 1));
        $persist = (bool) Arr::get($options, 'persist', false);

        $query = DB::table('engineering_order_items as oi')
            ->join('engineering_orders as o', 'o.id', '=', 'oi.order_id')
            ->selectRaw("oi.order_id, COALESCE(NULLIF(TRIM(oi.item_code), ''), CONCAT('product:', oi.product_id)) as item_code")
            ->where(function ($q) {
                $q->whereNotNull('oi.item_code')
                  ->orWhereNotNull('oi.product_id');
            });

        if ($from) {
            $query->whereDate('o.order_date', '>=', $from->toDateString());
        }

        if ($to) {
            $query->whereDate('o.order_date', '<=', $to->toDateString());
        }

        if (is_array($customerIds) && ! empty($customerIds)) {
            $query->whereIn('o.customer_id', $customerIds);
        }

        if (is_array($organizationIds) && ! empty($organizationIds)) {
            $query->whereIn('o.organization_id', $organizationIds);
        }

        $rows = $query
            ->orderBy('oi.order_id')
            ->get();

        $byOrder = [];

        foreach ($rows as $row) {
            $orderId = (int) $row->order_id;
            $code = (string) $row->item_code;
            if ($code === '' || $code === 'product:') {
                continue;
            }

            if (! isset($byOrder[$orderId])) {
                $byOrder[$orderId] = [];
            }

            $byOrder[$orderId][$code] = true; // deduplicate per order
        }

        $transactions = [];
        $orderIds = [];

        foreach ($byOrder as $orderId => $itemsSet) {
            $items = array_keys($itemsSet);
            if (count($items) < max(1, $minItems)) {
                continue;
            }

            sort($items, SORT_STRING);
            $transactions[] = $items;
            $orderIds[] = $orderId;
        }

        if ($persist && ! empty($transactions)) {
            $this->persistTransactions($orderIds, $transactions);
        }

        return $transactions;
    }

    protected function persistTransactions(array $orderIds, array $transactions): void
    {
        $now = Carbon::now();
        $inserts = [];

        foreach ($transactions as $idx => $items) {
            $inserts[] = [
                'order_id'   => $orderIds[$idx] ?? null,
                'items'      => json_encode(array_values($items), JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($inserts)) {
            DB::table('apriori_transactions')->insert($inserts);
        }
    }

    protected function toCarbonOrNull($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
