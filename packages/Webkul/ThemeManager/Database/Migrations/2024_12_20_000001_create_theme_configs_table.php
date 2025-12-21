<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_configs', function (Blueprint $table) {
            $table->id();

            // Ativação
            $table->boolean('is_active')->default(false);

            // Cores
            $table->string('color_primary', 20)->default('#1E40AF');
            $table->string('color_primary_dark', 20)->default('#1E3A8A');
            $table->string('color_primary_light', 20)->default('#3B82F6');
            $table->string('color_success', 20)->default('#10B981');
            $table->string('color_warning', 20)->default('#F59E0B');
            $table->string('color_danger', 20)->default('#EF4444');

            // Logos
            $table->string('logo_main', 500)->nullable();
            $table->string('logo_light', 500)->nullable();
            $table->string('logo_icon', 500)->nullable();
            $table->string('favicon', 500)->nullable();

            // Login Background
            $table->string('login_bg_image', 500)->nullable();
            $table->integer('login_bg_zoom')->default(100);
            $table->integer('login_bg_opacity')->default(50);
            $table->boolean('login_show_powered_by')->default(true);

            // Login Card Customizado
            $table->boolean('login_card_enabled')->default(false);
            $table->string('login_card_bg_image', 500)->nullable();
            $table->integer('login_card_bg_opacity')->default(62);
            $table->string('login_card_overlay_color', 50)->default('rgba(10, 45, 15, 0.78)');
            $table->string('login_card_title', 100)->default('Bem-vindo');
            $table->string('login_card_subtitle', 200)->default('Acesse sua conta para continuar');
            $table->boolean('login_card_sparkles')->default(false);
            $table->boolean('login_card_help_link')->default(true);
            $table->string('login_card_support_email', 100)->default('suporte@empresa.com.br');

            // Empty States
            $table->string('empty_state_activities', 500)->nullable();
            $table->string('empty_state_calls', 500)->nullable();
            $table->string('empty_state_emails', 500)->nullable();
            $table->string('empty_state_meetings', 500)->nullable();
            $table->string('empty_state_notes', 500)->nullable();
            $table->string('empty_state_organizations', 500)->nullable();
            $table->string('empty_state_persons', 500)->nullable();
            $table->string('empty_state_leads', 500)->nullable();
            $table->string('empty_state_products', 500)->nullable();

            $table->timestamps();
        });

        // Inserir registro padrão
        DB::table('theme_configs')->insert([
            'id' => 1,
            'is_active' => false,
            'color_primary' => '#1E40AF',
            'color_primary_dark' => '#1E3A8A',
            'color_primary_light' => '#3B82F6',
            'color_success' => '#10B981',
            'color_warning' => '#F59E0B',
            'color_danger' => '#EF4444',
            'login_bg_zoom' => 100,
            'login_bg_opacity' => 50,
            'login_show_powered_by' => true,
            'login_card_enabled' => false,
            'login_card_bg_opacity' => 62,
            'login_card_overlay_color' => 'rgba(10, 45, 15, 0.78)',
            'login_card_title' => 'Bem-vindo',
            'login_card_subtitle' => 'Acesse sua conta para continuar',
            'login_card_sparkles' => false,
            'login_card_help_link' => true,
            'login_card_support_email' => 'suporte@empresa.com.br',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_configs');
    }
};
