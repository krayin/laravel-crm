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
                'probability'      => 100,
                'sort_order'       => 1,
                'lead_stage_id'    => 1,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 2,
                'probability'      => 100,
                'sort_order'       => 2,
                'lead_stage_id'    => 2,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 3,
                'probability'      => 100,
                'sort_order'       => 3,
                'lead_stage_id'    => 3,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 4,
                'probability'      => 100,
                'sort_order'       => 4,
                'lead_stage_id'    => 4,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 5,
                'probability'      => 100,
                'sort_order'       => 5,
                'lead_stage_id'    => 5,
                'lead_pipeline_id' => 1,
            ], [
                'id'               => 6,
                'probability'      => 0,
                'sort_order'       => 6,
                'lead_stage_id'    => 6,
                'lead_pipeline_id' => 1,
            ]
        ]);
    }
}