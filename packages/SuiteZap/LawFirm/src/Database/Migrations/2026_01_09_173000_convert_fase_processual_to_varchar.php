<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations - Convert fase_processual ENUM to VARCHAR to allow 'Julgamento'
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `processos` MODIFY COLUMN `fase_processual` VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `processos` MODIFY COLUMN `fase_processual` ENUM('Inicial','Contestação','Réplica','Instrução','Sentença','Recurso','Execução') NULL");
    }
};
