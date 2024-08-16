<?php

namespace Webkul\Installer\Database\Seeders\EmailTemplate;

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
        $this->call(EmailTemplateSeeder::class, false, ['parameters' => $parameters]);
    }
}
