<?php

namespace Webkul\Installer\Database\Seeders\Workflow;

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
        $this->call(WorkflowSeeder::class, false, ['parameters' => $parameters]);
    }
}
