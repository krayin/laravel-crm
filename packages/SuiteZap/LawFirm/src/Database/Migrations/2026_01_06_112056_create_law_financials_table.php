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
        Schema::create('law_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->onDelete('cascade');
            $table->enum('tipo', ['receita', 'despesa'])->default('receita');
            $table->string('nome');
            $table->decimal('valor', 15, 2);
            $table->date('data_vencimento');
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_financials');
    }
};
