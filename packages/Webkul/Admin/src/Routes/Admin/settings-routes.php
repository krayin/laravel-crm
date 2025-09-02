<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Settings\AttributeController;
use Webkul\Admin\Http\Controllers\Settings\DataTransfer\ImportController;
use Webkul\Admin\Http\Controllers\Settings\EmailTemplateController;
use Webkul\Admin\Http\Controllers\Settings\GroupController;
use Webkul\Admin\Http\Controllers\Settings\LocationController;
use Webkul\Admin\Http\Controllers\Settings\Marketing\CampaignsController;
use Webkul\Admin\Http\Controllers\Settings\Marketing\EventController;
use Webkul\Admin\Http\Controllers\Settings\PipelineController;
use Webkul\Admin\Http\Controllers\Settings\RoleController;
use Webkul\Admin\Http\Controllers\Settings\SettingController;
use Webkul\Admin\Http\Controllers\Settings\SourceController;
use Webkul\Admin\Http\Controllers\Settings\TagController;
use Webkul\Admin\Http\Controllers\Settings\TypeController;
use Webkul\Admin\Http\Controllers\Settings\UserController;
use Webkul\Admin\Http\Controllers\Settings\Warehouse\ActivityController;
use Webkul\Admin\Http\Controllers\Settings\Warehouse\TagController as WarehouseTagController;
use Webkul\Admin\Http\Controllers\Settings\Warehouse\WarehouseController;
use Webkul\Admin\Http\Controllers\Settings\WebFormController;
use Webkul\Admin\Http\Controllers\Settings\WebhookController;
use Webkul\Admin\Http\Controllers\Settings\WorkflowController;

/**
 * Settings group routes.
 */
Route::prefix('settings')->group(function () {
    /**
     * Settings routes.
     */
    Route::controller(SettingController::class)->prefix('settings')->group(function () {
        Route::get('', 'index')->name('admin.settings.index');

        Route::get('search', 'search')->name('admin.settings.search');
    });

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
     * WebForms Routes.
     */
    Route::controller(WebFormController::class)->prefix('web-forms')->group(function () {
        Route::group(['middleware' => ['user']], function () {
            Route::get('', 'index')->name('admin.settings.web_forms.index');

            Route::get('create', 'create')->name('admin.settings.web_forms.create');

            Route::post('create', 'store')->name('admin.settings.web_forms.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.web_forms.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.web_forms.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.web_forms.delete');
        });
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

        Route::get('edit/{id}', 'edit')->name('admin.settings.tags.edit');

        Route::put('edit/{id}', 'update')->name('admin.settings.tags.update');

        Route::get('search', 'search')->name('admin.settings.tags.search');

        Route::delete('{id}', 'destroy')->name('admin.settings.tags.delete');

        Route::post('mass-destroy', 'massDestroy')->name('admin.settings.tags.mass_delete');
    });

    /**
     * Users Routes.
     */
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'index')->name('admin.settings.users.index');

        Route::post('create', 'store')->name('admin.settings.users.store');

        Route::get('edit/{id?}', 'edit')->name('admin.settings.users.edit');

        Route::put('edit/{id}', 'update')->name('admin.settings.users.update');

        Route::get('search', 'search')->name('admin.settings.users.search');

        Route::delete('{id}', 'destroy')->name('admin.settings.users.delete');

        Route::post('mass-update', 'massUpdate')->name('admin.settings.users.mass_update');

        Route::post('mass-destroy', 'massDestroy')->name('admin.settings.users.mass_delete');
    });

    /**
     * Pipelines Routes.
     */
    Route::controller(PipelineController::class)->prefix('pipelines')->group(function () {
        Route::get('', 'index')->name('admin.settings.pipelines.index');

        Route::get('create', 'create')->name('admin.settings.pipelines.create');

        Route::post('create', 'store')->name('admin.settings.pipelines.store');

        Route::get('edit/{id?}', 'edit')->name('admin.settings.pipelines.edit');

        Route::post('edit/{id}', 'update')->name('admin.settings.pipelines.update');

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

        Route::get('check-unique-validation', 'checkUniqueValidation')->name('admin.settings.attributes.check_unique_validation');

        Route::get('create', 'create')->name('admin.settings.attributes.create');

        Route::post('create', 'store')->name('admin.settings.attributes.store');

        Route::get('edit/{id}', 'edit')->name('admin.settings.attributes.edit');

        Route::put('edit/{id}', 'update')->name('admin.settings.attributes.update');

        Route::get('lookup/{lookup?}', 'lookup')->name('admin.settings.attributes.lookup');

        Route::get('lookup-entity/{lookup?}', 'lookupEntity')->name('admin.settings.attributes.lookup_entity');

        Route::delete('{id}', 'destroy')->name('admin.settings.attributes.delete');

        Route::get('{id}/options', 'getAttributeOptions')->name('admin.settings.attributes.options');

        Route::post('mass-update', 'massUpdate')->name('admin.settings.attributes.mass_update');

        Route::post('mass-destroy', 'massDestroy')->name('admin.settings.attributes.mass_delete');

        Route::get('download', 'download')->name('admin.settings.attributes.download');
    });

    /**
     * Warehouses Routes.
     */
    Route::controller(WarehouseController::class)->prefix('warehouses')->group(function () {
        Route::put('edit/{id}', 'update')->name('admin.settings.warehouses.update');

        Route::get('', 'index')->name('admin.settings.warehouses.index');

        Route::get('search', 'search')->name('admin.settings.warehouses.search');

        Route::get('{id}/products', 'products')->name('admin.settings.warehouses.products.index');

        Route::get('create', 'create')->name('admin.settings.warehouses.create');

        Route::post('create', 'store')->name('admin.settings.warehouses.store');

        Route::get('view/{id}', 'view')->name('admin.settings.warehouses.view');

        Route::get('edit/{id?}', 'edit')->name('admin.settings.warehouses.edit');

        Route::delete('{id}', 'destroy')->name('admin.settings.warehouses.delete');

        Route::controller(WarehouseTagController::class)->prefix('{id}/tags')->group(function () {
            Route::post('', 'attach')->name('admin.settings.warehouses.tags.attach');

            Route::delete('', 'detach')->name('admin.settings.warehouses.tags.detach');
        });

        Route::controller(ActivityController::class)->prefix('{id}/activities')->group(function () {
            Route::get('', 'index')->name('admin.settings.warehouse.activities.index');
        });
    });

    /**
     * Warehouses Location Routes.
     */
    Route::controller(LocationController::class)->prefix('locations')->group(function () {
        Route::get('search', 'search')->name('admin.settings.locations.search');

        Route::post('create', 'store')->name('admin.settings.locations.store');

        Route::put('edit/{id}', 'update')->name('admin.settings.locations.update');

        Route::delete('{id}', 'destroy')->name('admin.settings.locations.delete');
    });

    /**
     * Email Templates Routes.
     */
    Route::controller(EmailTemplateController::class)->prefix('email-templates')->group(function () {
        Route::get('', 'index')->name('admin.settings.email_templates.index');

        Route::get('create', 'create')->name('admin.settings.email_templates.create');

        Route::post('create', 'store')->name('admin.settings.email_templates.store');

        Route::get('edit/{id?}', 'edit')->name('admin.settings.email_templates.edit');

        Route::put('edit/{id}', 'update')->name('admin.settings.email_templates.update');

        Route::delete('{id}', 'destroy')->name('admin.settings.email_templates.delete');
    });

    /**
     * Events Routes.
     */
    Route::group(['prefix' => 'marketing'], function () {
        Route::controller(EventController::class)->prefix('events')->group(function () {
            Route::get('', 'index')->name('admin.settings.marketing.events.index');

            Route::post('create', 'store')->name('admin.settings.marketing.events.store');

            Route::get('edit/{id?}', 'edit')->name('admin.settings.marketing.events.edit');

            Route::put('edit/{id}', 'update')->name('admin.settings.marketing.events.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.marketing.events.delete');

            Route::post('mass-destroy', 'massDestroy')->name('admin.settings.marketing.events.mass_delete');
        });

        Route::controller(CampaignsController::class)->prefix('campaigns')->group(function () {
            Route::get('', 'index')->name('admin.settings.marketing.campaigns.index');

            Route::get('events', 'getEvents')->name('admin.settings.marketing.campaigns.events');

            Route::get('email-templates', 'getEmailTemplates')->name('admin.settings.marketing.campaigns.email-templates');

            Route::post('', 'store')->name('admin.settings.marketing.campaigns.store');

            Route::get('{id}', 'show')->name('admin.settings.marketing.campaigns.edit');

            Route::put('{id}', 'update')->name('admin.settings.marketing.campaigns.update');

            Route::delete('{id}', 'destroy')->name('admin.settings.marketing.campaigns.delete');

            Route::post('mass-destroy', 'massDestroy')->name('admin.settings.marketing.campaigns.mass_delete');
        });
    });

    Route::prefix('data-transfer')->group(function () {
        /**
         * Import routes.
         */
        Route::controller(ImportController::class)->prefix('imports')->group(function () {
            Route::get('', 'index')->name('admin.settings.data_transfer.imports.index');

            Route::get('create', 'create')->name('admin.settings.data_transfer.imports.create');

            Route::post('create', 'store')->name('admin.settings.data_transfer.imports.store');

            Route::get('edit/{id}', 'edit')->name('admin.settings.data_transfer.imports.edit');

            Route::put('update/{id}', 'update')->name('admin.settings.data_transfer.imports.update');

            Route::delete('destroy/{id}', 'destroy')->name('admin.settings.data_transfer.imports.delete');

            Route::get('import/{id}', 'import')->name('admin.settings.data_transfer.imports.import');

            Route::get('validate/{id}', 'validateImport')->name('admin.settings.data_transfer.imports.validate');

            Route::get('start/{id}', 'start')->name('admin.settings.data_transfer.imports.start');

            Route::get('link/{id}', 'link')->name('admin.settings.data_transfer.imports.link');

            Route::get('index/{id}', 'indexData')->name('admin.settings.data_transfer.imports.index_data');

            Route::get('stats/{id}/{state?}', 'stats')->name('admin.settings.data_transfer.imports.stats');

            Route::get('download-sample/{sample?}', 'downloadSample')->name('admin.settings.data_transfer.imports.download_sample');

            Route::get('download/{id}', 'download')->name('admin.settings.data_transfer.imports.download');

            Route::get('download-error-report/{id}', 'downloadErrorReport')->name('admin.settings.data_transfer.imports.download_error_report');
        });
    });
});
