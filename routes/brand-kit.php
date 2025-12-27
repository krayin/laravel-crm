<?php

use App\Http\Controllers\BrandKitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Brand Kit Routes
|--------------------------------------------------------------------------
|
| Rotas para gerenciamento do Brand Kit (overrides, custom CSS, snapshots).
| Protegidas por middleware de autenticação e permissão de admin.
|
*/

Route::prefix(config('app.admin_path', 'admin'))
    ->middleware(['web', 'user'])
    ->group(function () {
        Route::prefix('brand-kit')
            ->name('admin.brand-kit.')
            ->group(function () {
                // Config (leitura)
                Route::get('/config', [BrandKitController::class, 'config'])
                    ->name('config');

                Route::get('/themes', [BrandKitController::class, 'themes'])
                    ->name('themes');

                // Overrides
                Route::get('/overrides', [BrandKitController::class, 'overrides'])
                    ->name('overrides.index');

                Route::post('/overrides', [BrandKitController::class, 'storeOverride'])
                    ->name('overrides.store');

                Route::post('/overrides/batch', [BrandKitController::class, 'batchOverrides'])
                    ->name('overrides.batch');

                Route::delete('/overrides/{key}', [BrandKitController::class, 'deleteOverride'])
                    ->name('overrides.destroy');

                // Custom CSS
                Route::get('/css', [BrandKitController::class, 'customCss'])
                    ->name('css.index');

                Route::post('/css', [BrandKitController::class, 'storeCss'])
                    ->name('css.store');

                Route::patch('/css/{id}/toggle', [BrandKitController::class, 'toggleCss'])
                    ->name('css.toggle');

                Route::delete('/css/{id}', [BrandKitController::class, 'deleteCss'])
                    ->name('css.destroy');

                // Snapshots
                Route::get('/snapshots', [BrandKitController::class, 'snapshots'])
                    ->name('snapshots.index');

                Route::post('/snapshots', [BrandKitController::class, 'createSnapshot'])
                    ->name('snapshots.store');

                Route::post('/snapshots/{id}/restore', [BrandKitController::class, 'restoreSnapshot'])
                    ->name('snapshots.restore');

                Route::delete('/snapshots/{id}', [BrandKitController::class, 'deleteSnapshot'])
                    ->name('snapshots.destroy');

                // Reset
                Route::post('/reset', [BrandKitController::class, 'reset'])
                    ->name('reset');

                // Preview
                Route::post('/preview', [BrandKitController::class, 'preview'])
                    ->name('preview');

                // Cache
                Route::post('/cache/invalidate', [BrandKitController::class, 'invalidateCache'])
                    ->name('cache.invalidate');
            });
    });
