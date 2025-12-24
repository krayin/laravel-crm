<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona coluna previous_theme para suportar rollback de tema.
 * Upgrade-safe: não altera packages/, apenas adiciona coluna.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('theme_configs', function (Blueprint $table) {
            // Armazena o tema anterior para possibilitar rollback
            $table->string('previous_theme', 50)->nullable()->after('selected_theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_configs', function (Blueprint $table) {
            $table->dropColumn('previous_theme');
        });
    }
};
