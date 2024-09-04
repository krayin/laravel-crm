<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        DB::table('attributes')
            ->insert([
                [
                    'code'            => 'lead_pipeline_id',
                    'name'            => trans('installer::app.seeders.attributes.leads.pipeline'),
                    'type'            => 'lookup',
                    'entity_type'     => 'leads',
                    'lookup_type'     => 'lead_pipelines',
                    'validation'      => null,
                    'sort_order'      => '9',
                    'is_required'     => '1',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ], [
                    'code'            => 'lead_pipeline_stage_id',
                    'name'            => trans('installer::app.seeders.attributes.leads.stage'),
                    'type'            => 'lookup',
                    'entity_type'     => 'leads',
                    'lookup_type'     => 'lead_pipeline_stages',
                    'validation'      => null,
                    'sort_order'      => '10',
                    'is_required'     => '1',
                    'is_unique'       => '0',
                    'quick_add'       => '1',
                    'is_user_defined' => '0',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
