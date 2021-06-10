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
                'code'            => 'new',
                'name'            => 'New',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 2,
                'code'            => 'custom',
                'name'            => 'Follow Up',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 3,
                'code'            => 'custom',
                'name'            => 'Prospect',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 4,
                'code'            => 'custom',
                'name'            => 'Negotiation',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 5,
                'code'            => 'won',
                'name'            => 'Won',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], [
                'id'              => 6,
                'code'            => 'lost',
                'name'            => 'Lost',
                'is_user_defined' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]
        ]);
    }
}