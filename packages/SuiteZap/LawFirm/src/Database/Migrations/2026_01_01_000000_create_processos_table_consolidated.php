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
        Schema::create('processos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Campos de Identificação
            $table->string('titulo');
            $table->string('numero_cnj')->nullable()->unique();

            // Relacionamentos (Chaves)
            $table->unsignedInteger('lead_id')->nullable()->comment('Relacionamento com Lead (Oportunidade)');
            $table->unsignedInteger('person_id')->nullable()->comment('Relacionamento com Cliente');
            $table->unsignedInteger('user_id')->nullable()->comment('Advogado Responsável');

            // Detalhes do Processo
            $table->string('tribunal')->nullable();
            $table->string('vara')->nullable();
            $table->string('comarca')->nullable();
            $table->decimal('valor_causa', 15, 2)->nullable();
            $table->string('parte_contraria')->nullable();
            $table->string('advogado_parte_contraria')->nullable();
            $table->string('link_acesso')->nullable();

            // Datas
            $table->date('data_distribuicao')->nullable();
            $table->dateTime('data_audiencia')->nullable();

            // Campos ENUM (Padronização e Consistência)
            $table->enum('status', ['Ativo', 'Suspenso', 'Arquivado'])->default('Ativo');

            $table->enum('fase_processual', [
                'Inicial',
                'Contestação',
                'Réplica',
                'Instrução',
                'Sentença',
                'Recurso',
                'Execução'
            ])->nullable();

            $table->enum('area_direito', [
                'Civil',
                'Trabalhista',
                'Penal',
                'Tributário',
                'Família',
                'Consumidor',
                'Previdenciário'
            ])->nullable();

            $table->enum('probabilidade_exito', [
                'Alta',
                'Média',
                'Baixa'
            ])->nullable();

            // Descrição
            $table->text('descricao')->nullable();

            $table->timestamps();

            // Foreign Keys (Constraints)
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};
