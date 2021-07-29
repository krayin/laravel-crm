<?php

namespace Webkul\User\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            'id'              => 1,
            'name'            => 'Example Admin',
            'email'           => 'admin@example.com',
            'password'        => bcrypt('admin123'),
            // 'api_token'       => Str::random(80),
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
            'status'          => 1,
            'role_id'         => 1,
            'view_permission' => 'global',
        ]);
    }
}
