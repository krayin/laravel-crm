<?php

namespace Webkul\Installer\Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run($parameters = [])
    {
        DB::table('user_tenants')->delete();
        DB::table('users')->delete();

        DB::table('users')->insert([
            [
                'id'         => 1,
                'name'       => 'Example Admin',
                'email'      => 'flip@example.com',
                'password'   => bcrypt('Flip123@'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'Example Admin 2',
                'email'      => 'flip2@example.com',
                'password'   => bcrypt('Flip123@'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('user_tenants')->insert([
            [
                'user_id'         => 1,
                'tenant_id'       => 1,
                'role_id'         => 1,
                'status'          => 1,
                'view_permission' => 'global',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'user_id'         => 2,
                'tenant_id'       => 2,
                'role_id'         => 1,
                'status'          => 1,
                'view_permission' => 'global',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
