<?php

use Illuminate\Support\Facades\Route;
use Webkul\ThemeManager\Http\Controllers\ThemeController;

/**
 * ThemeManager routes.
 */
Route::group([
    'middleware' => ['web', 'admin_locale', 'user'],
    'prefix'     => config('app.admin_path'),
], function () {
    Route::prefix('settings')->group(function () {
        Route::controller(ThemeController::class)->group(function () {
            Route::get('theme', 'index')->name('admin.settings.theme.index');
            Route::post('theme', 'update')->name('admin.settings.theme.update');
            Route::post('theme/restore', 'restore')->name('admin.settings.theme.restore');
            Route::post('theme/rollback', 'rollback')->name('admin.settings.theme.rollback');
        });
    });
});
