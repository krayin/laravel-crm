<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Models\Product;
use Webkul\Warehouse\Models\Location;
use Webkul\Warehouse\Models\Warehouse;

class ProductInventoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Target Warehouse & Location
        $warehouse = Warehouse::where('name', 'Gudang Bandung')->first();

        if (! $warehouse) {
            return; // Skip if warehouse not seeded yet
        }

        $location = Location::where('warehouse_id', $warehouse->id)
            ->where('name', 'Lantai 1')
            ->first();

        if (! $location) {
            return; // Skip if location not seeded yet
        }

        $warehouseId = $warehouse->id;
        $locationId  = $location->id;

        // Set in_stock = 100 for all products at Gudang Bandung / Lantai 1
        $productIds = Product::pluck('id');

        foreach ($productIds as $productId) {
            DB::table('product_inventories')->updateOrInsert(
                [
                    'product_id'            => $productId,
                    'warehouse_id'          => $warehouseId,
                    'warehouse_location_id' => $locationId,
                ],
                [
                    'in_stock'   => 100,
                    'allocated'  => 0,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}

