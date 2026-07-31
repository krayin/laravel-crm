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
        Schema::create('contact_export_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('contact_export_batches')->cascadeOnDelete();
            $table->unsignedInteger('person_id');
            $table->string('status')->default('pending');
            $table->string('google_resource_name')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_export_batch_items');
    }
};
