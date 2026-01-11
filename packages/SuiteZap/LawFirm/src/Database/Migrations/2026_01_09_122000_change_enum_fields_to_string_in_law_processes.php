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
            // Change enum/restricted fields to nullable string
            // This allows values like "Julgamento", "Encerrado", etc.
            $table->string('status')->nullable()->change();
            $table->string('fase_processual')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed as we are widening the types
    }
};
