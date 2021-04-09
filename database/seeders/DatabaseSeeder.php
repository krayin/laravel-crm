<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\User\Database\Seeders\DatabaseSeeder as CRMSeeder;
use Webkul\Attribute\Database\Seeders\Database as AttributeDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(CRMSeeder::class);
        $this->call(AttributeDatabaseSeeder::class);
    }
}
