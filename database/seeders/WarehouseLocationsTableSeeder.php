<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Webkul\Warehouse\Models\Warehouse;
use Webkul\Warehouse\Models\Location;

class WarehouseLocationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Ensure target warehouse exists (created by WarehousesTableSeeder)
        $warehouse = Warehouse::where('name', 'Gudang Bandung')->first();
        if (! $warehouse) {
            // If warehouse not found, skip gracefully to avoid seeding failure.
            return;
        }

        // Create or update a location under the warehouse
        Location::updateOrCreate(
            [
                'warehouse_id' => $warehouse->id,
                'name'         => 'Lantai 1',
            ],
            [
                'warehouse_id' => $warehouse->id,
                'name'         => 'Lantai 1',
                'updated_at'   => $now,
                'created_at'   => $now,
            ]
        );
    }
}

