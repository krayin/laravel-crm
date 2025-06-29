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
        Schema::create('related_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->json('mobile_numbers')->nullable();
            $table->json('emails')->nullable();
            $table->date('eid_expiry')->nullable();
            $table->unsignedBigInteger('person_id');
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_contacts');
    }
};
