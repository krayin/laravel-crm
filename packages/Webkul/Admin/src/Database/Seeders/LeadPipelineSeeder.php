<?php

namespace Webkul\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadPipelineSeeder extends Seeder
{

    public function run()
    {
        DB::table('lead_pipelines')->delete();

        DB::table('lead_pipeline_stages')->delete();

        $now = Carbon::now();

        DB::table('lead_pipelines')->insert([
            [
                'id'         => 1,
                'name'       => 'Default Pipeline',
                'is_default' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);

        DB::table('lead_pipeline_stages')->insert([
            [
                'id'               => 1,
                'code'             => 'new',
                'name'             => 'New',
                'probability'      => 100,
                'sort_order'       => 1,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 2,
                'code'             => 'follow-up',
                'name'             => 'Follow Up',
                'probability'      => 100,
                'sort_order'       => 2,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 3,
                'code'             => 'prospect',
                'name'             => 'Prospect',
                'probability'      => 100,
                'sort_order'       => 3,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 4,
                'code'             => 'negotiation',
                'name'             => 'Negotiation',
                'probability'      => 100,
                'sort_order'       => 4,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 5,
                'code'             => 'won',
                'name'             => 'Won',
                'probability'      => 100,
                'sort_order'       => 5,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 6,
                'code'             => 'lost',
                'name'             => 'Lost',
                'probability'      => 0,
                'sort_order'       => 6,
                'lead_pipeline_id' => 1,
            ]
        ]);
    }
}