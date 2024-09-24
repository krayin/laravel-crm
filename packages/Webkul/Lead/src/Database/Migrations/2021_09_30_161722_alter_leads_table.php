<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablePrefix = DB::getTablePrefix();

        Schema::table('leads', function (Blueprint $table) {
            $table->integer('lead_pipeline_stage_id')->after('lead_pipeline_id')->unsigned()->nullable();
            $table->foreign('lead_pipeline_stage_id')->references('id')->on('lead_pipeline_stages')->onDelete('cascade');
        });

        DB::table('leads')
            ->update([
                'leads.lead_pipeline_stage_id' => DB::raw($tablePrefix.'leads.lead_stage_id'),
            ]);

        Schema::table('leads', function (Blueprint $table) use ($tablePrefix) {
            $table->dropForeign($tablePrefix.'leads_lead_stage_id_foreign');
            $table->dropColumn('lead_stage_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(DB::getTablePrefix().'leads_lead_pipeline_stage_id_foreign');
            $table->dropColumn('lead_pipeline_stage_id');

            $table->integer('lead_stage_id')->unsigned();
            $table->foreign('lead_stage_id')->references('id')->on('lead_stages')->onDelete('cascade');
        });
    }
};
