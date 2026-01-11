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
        Schema::create('law_processo_prazos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')
                ->constrained('processos') // Linked to 'processos' table as verified in Processo.php
                ->onDelete('cascade');

            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->dateTime('data_vencimento');
            $table->string('tipo')->default('comum'); // 'fatal', 'comum'
            $table->string('status')->default('pendente'); // 'pendente', 'concluido'
            $table->dateTime('concluido_em')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_processo_prazos');
    }
};
