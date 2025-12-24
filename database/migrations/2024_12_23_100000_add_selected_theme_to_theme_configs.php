<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('theme_configs', function (Blueprint $table) {
            $table->string('selected_theme', 50)
                  ->default('default')
                  ->after('is_active')
                  ->comment('Slug do tema selecionado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_configs', function (Blueprint $table) {
            $table->dropColumn('selected_theme');
        });
    }
};
