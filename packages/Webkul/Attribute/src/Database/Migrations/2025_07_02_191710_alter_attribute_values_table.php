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
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->string('unique_id')->nullable();
        });

        $tablePrefix = DB::getTablePrefix();

        DB::statement('UPDATE '.$tablePrefix."attribute_values SET unique_id = CONCAT(entity_id, '|', attribute_id)");

        Schema::table('attribute_values', function (Blueprint $table) {
            $table->unique('unique_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropUnique(['unique_id']);

            $table->dropColumn('unique_id');
        });
    }
};
