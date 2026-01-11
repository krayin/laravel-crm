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
        Schema::table('processos', function (Blueprint $table) {
            $table->string('tipo_parte')->nullable()->comment('pf or pj');
            $table->string('cpf_cnpj')->nullable();
            $table->string('advogado_oab')->nullable();
            $table->string('advogado_whatsapp')->nullable();
            $table->string('subarea_direito')->nullable();
            // User asked for fields already present? area_direito etc.
            // Migration request lists ONLY these 5.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_parte',
                'cpf_cnpj',
                'advogado_oab',
                'advogado_whatsapp',
                'subarea_direito',
            ]);
        });
    }
};
