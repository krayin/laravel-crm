<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Settings\GroupController;
use Webkul\Admin\Http\Controllers\Settings\RoleController;
use Webkul\Admin\Http\Controllers\Settings\SourceController;
use Webkul\Admin\Http\Controllers\Settings\TypeController;

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

        /**
         * Roles routes.
         */
        Route::controller(RoleController::class)->prefix('roles')->group(function () {
            Route::get('', 'index')->name('admin.settings.roles.index');

            Route::get('create', 'create')->name('admin.settings.roles.create');

            Route::post('create', 'store')->name('admin.settings.roles.store');

            Route::get('edit/{id}', 'edit')->name('admin.settings.roles.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.roles.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.roles.delete');
        });

        /**
         * Lead Sources routes.
         */
        Route::controller(SourceController::class)->prefix('sources')->group(function () {
            Route::get('', 'index')->name('admin.settings.sources.index');

            Route::post('create', 'store')->name('admin.settings.sources.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.sources.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.sources.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.sources.delete');
        });
    });
});
