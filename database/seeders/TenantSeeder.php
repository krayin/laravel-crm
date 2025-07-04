<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tenants')->delete();

        DB::table('domains')->delete();

        $tenant1 = Tenant::create([
            'id' => 1, 
            'name' => 'Tenant1',
            'lead_custom_fields_count' => 3
        ]);
        
        $tenant1->domains()->create(['domain' => 'tenant1.localhost']);
        $tenant2 = Tenant::create([
            'id' => 2, 
            'name' => 'Tenant2',
            'lead_custom_fields_count' => 2
        ]);
        
        $tenant2->domains()->create(['domain' => 'tenant2.localhost']);
    }
}
