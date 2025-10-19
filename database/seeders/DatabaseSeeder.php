<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Installer\Database\Seeders\DatabaseSeeder as KrayinDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(KrayinDatabaseSeeder::class);

        // Seed Organizations (from provided dataset) then Persons (PIC per org)
        $this->call(OrganizationsTableSeeder::class);
        $this->call(PersonsFromOrganizationsSeeder::class);

        // Seed Warehouses (Krayin core schema)
        $this->call(WarehousesTableSeeder::class);
        $this->call(WarehouseLocationsTableSeeder::class);

        // Seed Products
        $this->call(ProductsTableSeeder::class);

        // Seed initial Product Inventories for Gudang Bandung / Lantai 1
        $this->call(ProductInventoriesTableSeeder::class);

        // Seed demo data for Analytical CRM (engineering orders & items)
        $this->call(AnalyticalCrmDemoSeeder::class);

        // Seed demo Leads & Quotes dataset (300 pairs)
        $this->call(LeadQuoteDemoSeeder::class);
    }
}
