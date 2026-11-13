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
        Schema::create('lead_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lead_id'); // Match leads.id type (int unsigned)
            $table->integer('project_number'); // 1, 2, 3...
            $table->string('project_name');
            $table->string('google_drive_folder_id')->nullable();
            $table->string('google_drive_folder_url')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            
            // Ensure unique project numbers per lead
            $table->unique(['lead_id', 'project_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_projects');
    }
};
