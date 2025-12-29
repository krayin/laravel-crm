<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Trait para criar schema mínimo do BrandKit em memória.
 *
 * Usa SQLite :memory: para testes isolados, sem depender
 * das migrations completas do Krayin.
 *
 * Colunas usadas pelo BrandKitResolver/Model:
 * - brand_kit_overrides: override_key, value (não field_key/field_value)
 * - brand_kit_custom_css: target, priority, is_enabled
 * - brand_kit_snapshots: overrides_data, custom_css_data, is_auto
 * - theme_configs: todas as colunas de cores/logos/login
 */
trait BrandKitTestSchema
{
    /**
     * Configura o banco em memória e cria as tabelas necessárias.
     * Deve ser chamado no setUp() do teste.
     */
    protected function setUpBrandKitSchema(): void
    {
        // Força SQLite em memória para este teste
        config(['database.default' => 'testing']);
        config(['database.connections.testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        // Reconecta ao banco de testes
        DB::purge('testing');
        DB::reconnect('testing');

        // Cria as tabelas necessárias
        $this->createBrandKitOverridesTable();
        $this->createBrandKitSnapshotsTable();
        $this->createBrandKitCustomCssTable();
        $this->createThemeConfigsTable();

        // Insere linha default (id=1) usada pelo BrandKitResolver
        $this->insertDefaultThemeConfig();
    }

    /**
     * Limpa o banco após o teste.
     * Deve ser chamado no tearDown() do teste.
     */
    protected function tearDownBrandKitSchema(): void
    {
        Schema::dropIfExists('brand_kit_overrides');
        Schema::dropIfExists('brand_kit_snapshots');
        Schema::dropIfExists('brand_kit_custom_css');
        Schema::dropIfExists('theme_configs');
    }

    private function createBrandKitOverridesTable(): void
    {
        if (Schema::hasTable('brand_kit_overrides')) {
            return;
        }

        Schema::create('brand_kit_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('scope_key', 100)->default('global');
            $table->string('theme_slug', 100)->default('default');
            $table->string('override_key', 100);
            $table->text('value')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['scope_key', 'theme_slug', 'override_key'], 'bko_unique');
            $table->index(['scope_key', 'theme_slug', 'is_active'], 'bko_lookup');
        });
    }

    private function createBrandKitSnapshotsTable(): void
    {
        if (Schema::hasTable('brand_kit_snapshots')) {
            return;
        }

        Schema::create('brand_kit_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('scope_key', 100)->default('global');
            $table->string('theme_slug', 100)->default('default');
            $table->string('name', 200);
            $table->unsignedTinyInteger('snapshot_version')->default(1);
            $table->json('overrides_data')->nullable();
            $table->json('custom_css_data')->nullable();
            $table->boolean('is_auto')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['scope_key', 'theme_slug', 'is_auto'], 'bks_lookup');
        });
    }

    private function createBrandKitCustomCssTable(): void
    {
        if (Schema::hasTable('brand_kit_custom_css')) {
            return;
        }

        Schema::create('brand_kit_custom_css', function (Blueprint $table) {
            $table->id();
            $table->string('scope_key', 100)->default('global');
            $table->string('theme_slug', 100)->default('default');
            $table->string('name', 200);
            $table->text('css_content');
            $table->string('target', 20)->default('admin'); // admin|login|both
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('priority')->default(100);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['scope_key', 'theme_slug', 'is_enabled', 'target'], 'bkc_lookup');
        });
    }

    private function createThemeConfigsTable(): void
    {
        if (Schema::hasTable('theme_configs')) {
            return;
        }

        Schema::create('theme_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('selected_theme', 100)->default('default');

            // Cores
            $table->string('color_primary', 20)->nullable();
            $table->string('color_primary_dark', 20)->nullable();
            $table->string('color_primary_light', 20)->nullable();
            $table->string('color_success', 20)->nullable();
            $table->string('color_warning', 20)->nullable();
            $table->string('color_danger', 20)->nullable();

            // Logos
            $table->string('logo_main', 500)->nullable();
            $table->string('logo_light', 500)->nullable();
            $table->string('logo_icon', 500)->nullable();
            $table->string('favicon', 500)->nullable();

            // Login
            $table->string('login_bg_image', 500)->nullable();
            $table->unsignedTinyInteger('login_bg_zoom')->default(100);
            $table->unsignedTinyInteger('login_bg_opacity')->default(50);
            $table->boolean('login_show_powered_by')->default(true);
            $table->boolean('login_card_enabled')->default(false);
            $table->string('login_card_bg_image', 500)->nullable();
            $table->unsignedTinyInteger('login_card_bg_opacity')->default(62);
            $table->string('login_card_overlay_color', 50)->nullable();
            $table->string('login_card_title', 100)->nullable();
            $table->string('login_card_subtitle', 200)->nullable();
            $table->boolean('login_card_sparkles')->default(false);
            $table->boolean('login_card_help_link')->default(true);
            $table->string('login_card_support_email', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Insere linha default (id=1) na theme_configs.
     * Necessário para testes que dependem de ThemeConfig::getInstance().
     */
    private function insertDefaultThemeConfig(): void
    {
        DB::table('theme_configs')->insert([
            'id' => 1,
            'is_active' => false,
            'selected_theme' => 'default',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
