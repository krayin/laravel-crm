<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Setting\TypeController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin_locale'], 'prefix' => config('app.admin_path')], function () {
    Route::prefix('settings')->group(function () {
        /**
         * Type routes.
         */
        Route::controller(TypeController::class)->prefix('types')->group(function () {
            Route::get('', 'index')->name('admin.settings.types.index');

            Route::post('create', 'store')->name('admin.settings.types.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.types.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.types.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.types.delete');
        });
    });
});
