<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Warehouse\Models\Warehouse;

class WarehousesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $warehouses = [
            [
                'name'            => 'Gudang Bandung',
                'description'     => '',
                'contact_name'    => 'PIC Gudang',
                'contact_emails'  => [
                    ['value' => 'gudang-test@gmail.com', 'label' => 'work'],
                ],
                'contact_numbers' => [
                    ['value' => '08324876429', 'label' => 'work'],
                ],
                'contact_address' => [
                    'address'  => 'JL. Paledang No. 12',
                    'country'  => 'ID',
                    'state'    => 'Jawa Barat',
                    'city'     => 'Bandung',
                    'postcode' => '40123',
                ],
            ],
        ];

        $attrValues = app(AttributeValueRepository::class);

        foreach ($warehouses as $w) {
            $existing = Warehouse::where('name', $w['name'])->first();

            if ($existing) {
                $existing->fill([
                    'description'      => $w['description'],
                    'contact_name'     => $w['contact_name'],
                    'contact_emails'   => $w['contact_emails'],
                    'contact_numbers'  => $w['contact_numbers'],
                    'contact_address'  => $w['contact_address'],
                    'updated_at'       => $now,
                ]);
                $existing->save(); // fires Updated activity via LogsActivity
                $warehouse   = $existing;
            } else {
                $warehouse = Warehouse::create([
                    'name'             => $w['name'],
                    'description'      => $w['description'],
                    'contact_name'     => $w['contact_name'],
                    'contact_emails'   => $w['contact_emails'],
                    'contact_numbers'  => $w['contact_numbers'],
                    'contact_address'  => $w['contact_address'],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]); // fires Created activity via LogsActivity
            }

            $warehouseId = $warehouse->id;

            // Ensure EAV values exist/updated so UI shows values in forms/lookups
            $attrValues->save([
                'entity_type'      => 'warehouses',
                'entity_id'        => $warehouseId,
                'name'             => $w['name'],
                'description'      => $w['description'],
                'contact_name'     => $w['contact_name'],
                'contact_emails'   => $w['contact_emails'],
                'contact_numbers'  => $w['contact_numbers'],
                'contact_address'  => $w['contact_address'],
            ]);
        }
    }
}
