<?php

namespace App\Providers;

use App\Http\Controllers\Admin\Settings\ThemeSettingsController;
use App\Observers\ThemeConfigObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * ThemeOverridesServiceProvider - Registra rotas override para Theme Settings.
 *
 * Este provider deve ser registrado DEPOIS do ThemeManagerServiceProvider
 * para que suas rotas tenham prioridade (última rota registrada prevalece
 * quando há conflito de nome).
 *
 * IMPORTANTE: Registrar este provider no FINAL de config/app.php providers.
 *
 * Também registra o ThemeConfigObserver para garantir invalidação de cache
 * em QUALQUER caminho de código (UI, CLI, API, scripts).
 */
class ThemeOverridesServiceProvider extends ServiceProvider
{
    /**
     * FQCN do Model ThemeConfig do pacote.
     */
    private const THEME_CONFIG_MODEL = \Webkul\ThemeManager\Models\ThemeConfig::class;

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerThemeSettingsRoutes();
        $this->registerThemeConfigObserver();
    }

    /**
     * Registra o Observer para ThemeConfig (invalidação universal de cache).
     *
     * Safe para CLI: não quebra se a tabela não existir ainda.
     */
    protected function registerThemeConfigObserver(): void
    {
        // Só registra se a classe do Model existir (upgrade-safe)
        if (! class_exists(self::THEME_CONFIG_MODEL)) {
            return;
        }

        try {
            self::THEME_CONFIG_MODEL::observe(ThemeConfigObserver::class);
        } catch (\Throwable $e) {
            // Falha silenciosa em CLI/migrations se tabela não existir
            // O observer será registrado na próxima request após migrate
        }
    }

    /**
     * Registra rotas override para Theme Settings.
     *
     * Usa os mesmos middlewares, prefix e route names do pacote original,
     * mas aponta para o controller em app/.
     */
    protected function registerThemeSettingsRoutes(): void
    {
        Route::group([
            'middleware' => ['web', 'admin_locale', 'user'],
            'prefix'     => config('app.admin_path', 'admin'),
        ], function () {
            Route::prefix('settings')->group(function () {
                Route::controller(ThemeSettingsController::class)->group(function () {
                    Route::get('theme', 'index')->name('admin.settings.theme.index');
                    Route::post('theme', 'update')->name('admin.settings.theme.update');
                    Route::post('theme/restore', 'restore')->name('admin.settings.theme.restore');
                    Route::post('theme/rollback', 'rollback')->name('admin.settings.theme.rollback');
                    Route::post('theme/reset-field', 'resetField')->name('admin.settings.theme.reset-field');
                });
            });
        });
    }
}
