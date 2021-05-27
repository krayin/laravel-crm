<?php

namespace Webkul\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadTypeSeeder extends Seeder
{

    public function run()
    {
        DB::table('lead_types')->delete();
        
        $now = Carbon::now();

        DB::table('lead_types')->insert([
            [
                'id'              => 1,
                'name'            => 'Email',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]
        ]);
    }
}