<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Setting\TypeController;
use Webkul\Admin\Http\Controllers\Setting\WebhookController;
use Webkul\Admin\Http\Controllers\Setting\WorkflowController;

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

        /**
         * Webhook Routes.
         */
        Route::controller(WorkflowController::class)->prefix('workflows')->group(function () {
            Route::get('', 'index')->name('admin.settings.workflows.index');

            Route::get('create', 'create')->name('admin.settings.workflows.create');

            Route::post('create', 'store')->name('admin.settings.workflows.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.workflows.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.workflows.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.workflows.delete');
        });

        /**
         * Workflows Routes.
         */
        Route::controller(WebhookController::class)->prefix('webhooks')->group(function () {
            Route::get('', 'index')->name('admin.settings.webhooks.index');

            Route::get('create', 'create')->name('admin.settings.webhooks.create');

            Route::post('create', 'store')->name('admin.settings.webhooks.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.webhooks.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.webhooks.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.webhooks.delete');
        });
    });
});
