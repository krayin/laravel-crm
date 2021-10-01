<?php

namespace Webkul\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AttributeSeeder::class);
        $this->call(EmailTemplateSeeder::class);
        $this->call(LeadPipelineSeeder::class);
        $this->call(LeadTypeSeeder::class);
        $this->call(LeadSourceSeeder::class);
        $this->call(WorkflowSeeder::class);
    }
}
