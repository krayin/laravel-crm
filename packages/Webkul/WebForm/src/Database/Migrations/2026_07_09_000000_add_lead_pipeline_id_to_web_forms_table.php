<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('web_forms', function (Blueprint $table) {
            $table->integer('lead_pipeline_id')->unsigned()->nullable()->after('create_lead');

            $table->foreign('lead_pipeline_id')->references('id')->on('lead_pipelines')->onDelete('set null');
        });

        /**
         * The pipeline of a lead is decided by the web form itself, so the attributes which were
         * already added to the existing web forms are detached to stop them from being asked to
         * the visitor on the public form.
         */
        $restrictedAttributeIds = DB::table('attributes')
            ->whereIn('code', ['lead_pipeline_id', 'lead_pipeline_stage_id'])
            ->pluck('id');

        DB::table('web_form_attributes')
            ->whereIn('attribute_id', $restrictedAttributeIds)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_forms', function (Blueprint $table) {
            $table->dropForeign(['lead_pipeline_id']);

            $table->dropColumn('lead_pipeline_id');
        });
    }
};
