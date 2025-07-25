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
        Schema::table('activity_files', function (Blueprint $table) {
            $table->string('file_code')->nullable()->after('id');
            $table->unsignedBigInteger('entity_id')->nullable()->after('file_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['file_code', 'entity_id']);
        });
    }
};
