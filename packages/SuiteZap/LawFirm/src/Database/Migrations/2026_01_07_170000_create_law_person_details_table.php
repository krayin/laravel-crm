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
        Schema::create('law_person_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('person_id')->unique();
            $table->enum('type', ['PF', 'PJ'])->default('PF');

            // Campos Pessoa Física (PF)
            $table->string('cpf', 14)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('rg_orgao', 20)->nullable();
            $table->string('rg_uf', 2)->nullable();
            $table->string('nacionalidade', 50)->nullable();
            $table->string('estado_civil', 30)->nullable();
            $table->string('profissao', 100)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('nome_mae', 150)->nullable();
            $table->string('nome_pai', 150)->nullable();

            // Campos Pessoa Jurídica (PJ)
            $table->string('cnpj', 18)->nullable();
            $table->string('razao_social', 200)->nullable();
            $table->string('inscricao_estadual', 30)->nullable();
            $table->string('inscricao_municipal', 30)->nullable();
            $table->string('cnae', 20)->nullable();
            $table->string('representante_legal', 150)->nullable();

            // Campos de Endereço
            $table->string('cep', 9)->nullable();
            $table->string('logradouro', 200)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();

            $table->timestamps();

            $table->foreign('person_id')
                ->references('id')
                ->on('persons')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_person_details');
    }
};
