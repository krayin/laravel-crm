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
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('client_number')->nullable()->unique()->after('id');
            $table->string('google_drive_folder_id')->nullable()->after('client_number');
            $table->string('google_drive_folder_url')->nullable()->after('google_drive_folder_id');
            $table->timestamp('folder_created_at')->nullable()->after('google_drive_folder_url');
            $table->timestamp('folder_moved_at')->nullable()->after('folder_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'client_number',
                'google_drive_folder_id',
                'google_drive_folder_url',
                'folder_created_at',
                'folder_moved_at'
            ]);
        });
    }
};
