<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1. Tabela de Templates (Os "Kits" pré-definidos)
        Schema::create('law_checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Kit Trabalhista
            $table->string('area')->nullable(); // Ex: trabalhista, civel (para filtro)
            $table->json('items'); // Lista dos documentos: ["RG", "CTPS", "TRCT"]
            $table->timestamps();
        });

        // 2. Tabela de Documentos do Processo (O Checklist Real)
        Schema::create('law_process_documents', function (Blueprint $table) {
            $table->id();

            // Vínculo com o Processo
            $table->foreignId('processo_id')
                ->constrained('processos')
                ->onDelete('cascade');

            $table->string('name'); // Nome do documento exigido (Ex: RG)
            $table->string('status')->default('pending'); // pending, received, approved, rejected
            $table->string('file_path')->nullable(); // Caminho do arquivo se já tiver feito upload
            $table->text('notes')->nullable(); // Obs do advogado

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('law_process_documents');
        Schema::dropIfExists('law_checklist_templates');
    }
};
