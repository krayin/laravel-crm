<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Settings\TagController;
use Webkul\Admin\Http\Controllers\Settings\RoleController;
use Webkul\Admin\Http\Controllers\Settings\TypeController;
use Webkul\Admin\Http\Controllers\Settings\UserController;
use Webkul\Admin\Http\Controllers\Settings\GroupController;
use Webkul\Admin\Http\Controllers\Settings\SourceController;
use Webkul\Admin\Http\Controllers\Settings\WebhookController;
use Webkul\Admin\Http\Controllers\Settings\PipelineController;
use Webkul\Admin\Http\Controllers\Settings\WorkflowController;
use Webkul\Admin\Http\Controllers\Settings\AttributeController;

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
         * Workflows Routes.
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
         * Webhook Routes.
         */
        Route::controller(WebhookController::class)->prefix('webhooks')->group(function () {
            Route::get('', 'index')->name('admin.settings.webhooks.index');

            Route::get('create', 'create')->name('admin.settings.webhooks.create');

            Route::post('create', 'store')->name('admin.settings.webhooks.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.webhooks.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.webhooks.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.webhooks.delete');
        });

        /**
         * Tags Routes.
         */
        Route::controller(TagController::class)->prefix('tags')->group(function () {
            Route::get('', 'index')->name('admin.settings.tags.index');

            Route::post('create', 'store')->name('admin.settings.tags.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.tags.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.tags.update');

            Route::get('search', 'search')->name('admin.settings.tags.search');

            Route::delete('{id}', 'destroy')->name('admin.settings.tags.delete');

            Route::put('mass-destroy', 'massDestroy')->name('admin.settings.tags.mass_delete');
        });

        /**
         * Users Routes.
         */
        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::get('', 'index')->name('admin.settings.users.index');

            Route::get('create', 'create')->name('admin.settings.users.create');

            Route::post('create', 'store')->name('admin.settings.users.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.users.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.users.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.users.delete');

            Route::put('mass-update', 'massUpdate')->name('admin.settings.users.mass_update');

            Route::put('mass-destroy', 'massDestroy')->name('admin.settings.users.mass_delete');
        });

        /**
         * Pipelines Routes.
         */
        Route::controller(PipelineController::class)->prefix('pipelines')->group(function () {
            Route::get('', 'index')->name('admin.settings.pipelines.index');

            Route::get('create', 'create')->name('admin.settings.pipelines.create');

            Route::post('create', 'store')->name('admin.settings.pipelines.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.pipelines.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.pipelines.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.pipelines.delete');
        });

        /**
         * Sources Routes.
         */
        Route::controller(SourceController::class)->prefix('sources')->group(function () {
            Route::get('', 'index')->name('admin.settings.sources.index');

            Route::post('create', 'store')->name('admin.settings.sources.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.sources.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.sources.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.sources.delete');
        });

        /**
         * Attributes Routes.
         */
        Route::controller(AttributeController::class)->prefix('attributes')->group(function () {
            Route::get('', 'index')->name('admin.settings.attributes.index');

            Route::get('create', 'create')->name('admin.settings.attributes.create');

            Route::post('create', 'store')->name('admin.settings.attributes.store');

            Route::get('edit/{id}', 'edit')->name('admin.settings.attributes.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.attributes.update');

            Route::get('lookup/{lookup?}', 'lookup')->name('admin.settings.attributes.lookup');

            Route::get('lookup-entity/{lookup?}', 'lookupEntity')->name('admin.settings.attributes.lookup_entity');

            Route::delete('{id}', 'destroy')->name('admin.settings.attributes.delete');

            Route::put('mass-update', 'massUpdate')->name('admin.settings.attributes.mass_update');

            Route::put('mass-destroy', 'massDestroy')->name('admin.settings.attributes.mass_delete');

            Route::get('download', 'download')->name('admin.settings.attributes.download');
        });
    });
});
