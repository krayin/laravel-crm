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
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->unsignedInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $tablePrefix = DB::getTablePrefix();

            // Disable foreign key checks temporarily.
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Drop the foreign key constraint using raw SQL.
            DB::statement('ALTER TABLE '.$tablePrefix.'activities DROP FOREIGN KEY activities_user_id_foreign');

            // Drop the index.
            DB::statement('ALTER TABLE '.$tablePrefix.'activities DROP INDEX activities_user_id_foreign');

            // Change the column to be non-nullable.
            $table->unsignedInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Re-enable foreign key checks.
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }
};
