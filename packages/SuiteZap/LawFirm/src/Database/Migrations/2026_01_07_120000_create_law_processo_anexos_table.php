<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('law_processo_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')
                ->constrained('processos')
                ->onDelete('cascade');
            $table->string('path');
            $table->string('nome_original');
            $table->string('tipo_mime')->nullable();
            $table->integer('tamanho')->nullable(); // Size in bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_processo_anexos');
    }
};
