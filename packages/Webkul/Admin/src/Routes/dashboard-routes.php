<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\DashboardController;

/**
 * Extra routes.
 */
Route::group(['middleware' => ['web', 'user', 'admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Dashboard routes.
     */
    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('', 'index')->name('admin.dashboard.index');

        Route::get('stats', 'stats')->name('admin.dashboard.stats');
    });
});
