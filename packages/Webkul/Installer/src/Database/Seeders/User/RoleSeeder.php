<?php

namespace Webkul\Installer\Database\Seeders\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('users')->delete();

        DB::table('roles')->delete();

        DB::table('roles')->insert([
            'id'              => 1,
            'name'            => 'Administrator',
            'description'     => 'Administrator role',
            'permission_type' => 'all',
        ]);
    }
}