<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $tenant1 = Tenant::create(['id' => '1', 'name' => 'Tenant1']);
        $tenant1->domains()->create(['domain' => 'tenant1.localhost']);
        $tenant2 = Tenant::create(['id' => '2', 'name' => 'Tenant2']);
        $tenant2->domains()->create(['domain' => 'tenant2.localhost']);
    }
}
