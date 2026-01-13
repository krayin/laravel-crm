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
        // Primeiro, converte strings vazias para NULL
        \DB::statement("UPDATE processos SET numero_cnj = NULL WHERE numero_cnj = ''");

        Schema::table('processos', function (Blueprint $table) {
            // Remove a constraint unique
            $table->dropUnique('processos_numero_cnj_unique');

            // Torna o campo nullable (caso ainda não seja)
            $table->string('numero_cnj')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            // Recria a unique constraint
            $table->unique('numero_cnj');
        });
    }
};
