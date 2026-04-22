<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'parent_activity_id')) {
                $table->unsignedInteger('parent_activity_id')->nullable()->after('user_id');
            }

            $foreignKeys = collect(Schema::getForeignKeys('activities'))
                ->pluck('name');

            if (! $foreignKeys->contains('activities_parent_activity_id_foreign')) {
                $table->foreign('parent_activity_id')
                    ->references('id')
                    ->on('activities')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['parent_activity_id']);
            $table->dropColumn('parent_activity_id');
        });
    }
};
