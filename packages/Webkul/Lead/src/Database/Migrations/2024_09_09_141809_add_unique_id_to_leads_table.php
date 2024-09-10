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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('unique_id')->nullable()->unique()->after('updated_at');
        });

        DB::statement("
            UPDATE leads
            SET unique_id = CONCAT(
                user_id, '|', person_id, '|', lead_source_id, '|', lead_type_id, '|', 
                IFNULL(lead_pipeline_id, 'NULL')
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('unique_id');
            });
        });
    }
};
