<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Settings\TypeController;
use Webkul\Admin\Http\Controllers\Settings\GroupController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin_locale'], 'prefix' => config('app.admin_path')], function () {
    Route::prefix('settings')->group(function () {
        /**
         * Groups routes.
         */
        Route::controller(GroupController::class)->prefix('groups')->group(function () {
            Route::get('', 'index')->name('admin.settings.groups.index');

            Route::get('create', 'create')->name('admin.settings.groups.create');

            Route::post('create', 'store')->name('admin.settings.groups.store');

            Route::get('edit/{id}', 'edit')->name('admin.settings.groups.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.groups.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.groups.delete');
        });

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
