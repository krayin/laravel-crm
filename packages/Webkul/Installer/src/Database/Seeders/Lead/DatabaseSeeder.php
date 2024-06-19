<?php

namespace Webkul\Installer\Database\Seeders\Lead;

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
        $this->call(PipelineSeeder::class, false, ['parameters' => $parameters]);
        $this->call(TypeSeeder::class, false, ['parameters' => $parameters]);
        $this->call(SourceSeeder::class, false, ['parameters' => $parameters]);
    }
}
