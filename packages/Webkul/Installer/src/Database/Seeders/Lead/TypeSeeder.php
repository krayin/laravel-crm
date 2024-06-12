<?php

namespace Webkul\Installer\Database\Seeders\Lead;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('lead_types')->delete();
        
        $now = Carbon::now();

        DB::table('lead_types')->insert([
            [
                'id'         => 1,
                'name'       => 'New Business',
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 2,
                'name'       => 'Existing Business',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}