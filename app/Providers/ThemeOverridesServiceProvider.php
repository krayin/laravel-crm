<?php

namespace App\Providers;

use App\Http\Controllers\Admin\Settings\ThemeSettingsController;
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
 */
class ThemeOverridesServiceProvider extends ServiceProvider
{
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
