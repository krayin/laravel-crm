<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalyticalCrmDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tag = 'DEMO: AnalyticalCRM';

        // Cleanup previous demo data (idempotent seeder)
        DB::table('engineering_orders')
            ->where('notes', 'like', $tag.'%')
            ->delete();

        $now = Carbon::now();

        // Define catalog of items (codes)
        $PUMP   = 'PUMP';
        $VALVE  = 'VALVE';
        $SEAL   = 'SEAL';
        $GAUGE  = 'GAUGE';
        $CLAMP  = 'CLAMP';
        $HOSE   = 'HOSE';
        $FILTER = 'FILTER';
        $GASKET = 'GASKET';

        $baskets = [];

        // Strong associations
        // 1) PUMP -> VALVE (+ SEAL often)
        for ($i = 0; $i < 20; $i++) {
            $baskets[] = [$PUMP, $VALVE, $SEAL];
        }
        for ($i = 0; $i < 10; $i++) {
            $baskets[] = [$PUMP, $VALVE];
        }

        // 2) VALVE -> SEAL
        for ($i = 0; $i < 15; $i++) {
            $baskets[] = [$VALVE, $SEAL];
        }

        // 3) HOSE -> CLAMP
        for ($i = 0; $i < 12; $i++) {
            $baskets[] = [$HOSE, $CLAMP];
        }

        // Additional combinations / noise
        for ($i = 0; $i < 10; $i++) {
            $baskets[] = [$PUMP, $GAUGE];
        }
        for ($i = 0; $i < 6; $i++) {
            $baskets[] = [$FILTER, $GASKET];
        }
        for ($i = 0; $i < 7; $i++) {
            $baskets[] = [$SEAL, $GAUGE];
        }
        for ($i = 0; $i < 8; $i++) {
            $baskets[] = [$PUMP, $FILTER];
        }
        for ($i = 0; $i < 5; $i++) {
            $baskets[] = [$GASKET];
        }

        // Shuffle to distribute over time
        shuffle($baskets);

        // Insert orders + items
        foreach ($baskets as $idx => $items) {
            $orderDate = $now->copy()->subDays(rand(0, 90));

            $orderId = DB::table('engineering_orders')->insertGetId([
                'customer_id'    => null,
                'organization_id'=> null,
                'order_date'     => $orderDate->toDateString(),
                'status'         => 'completed',
                'notes'          => $tag.' #'.($idx + 1),
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            foreach ($items as $code) {
                DB::table('engineering_order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => null,
                    'item_code'  => $code,
                    'qty'        => rand(1, 3),
                    'unit_price' => match ($code) {
                        'PUMP'   => 2500000,
                        'VALVE'  => 750000,
                        'SEAL'   => 250000,
                        'GAUGE'  => 400000,
                        'CLAMP'  => 150000,
                        'HOSE'   => 300000,
                        'FILTER' => 200000,
                        'GASKET' => 120000,
                        default  => 100000,
                    },
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}

