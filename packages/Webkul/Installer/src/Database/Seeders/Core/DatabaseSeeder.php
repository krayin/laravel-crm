<?php

namespace Webkul\Installer\Database\Seeders\Core;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        $this->call(CountrySeeder::class, false, ['parameters' => $parameters]);
        $this->call(StateSeeder::class, false, ['parameters' => $parameters]);
    }
}
