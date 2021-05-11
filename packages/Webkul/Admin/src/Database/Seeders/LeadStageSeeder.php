<?php

namespace Webkul\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStageSeeder extends Seeder
{

    public function run()
    {
        DB::table('lead_stages')->delete();
        
        $now = Carbon::now();

        DB::table('lead_stages')->insert([
            [
                'id'              => 1,
                'name'            => 'New',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 2,
                'name'            => 'Won',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 3,
                'name'            => 'Lost',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]
        ]);
    }
}