<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\DataGrid\SavedFilterController;

Route::group(['middleware' => ['web', 'admin_locale']], function () {
    Route::get('/', 'Webkul\Admin\Http\Controllers\Controller@redirectToLogin')->name('krayin.home');

    Route::get('create/{id}', 'Webkul\Admin\Http\Controllers\DataGrid\SavedFilterController@create')->name('admin.datagrid.saved_filters.destroy');

    Route::prefix(config('app.admin_path'))->group(function () {
        // Admin Routes
        Route::group(['middleware' => ['user']], function () {
            Route::delete('logout', 'Webkul\Admin\Http\Controllers\User\SessionController@destroy')->name('admin.session.destroy');

            // Dashboard Route
            Route::get('dashboard', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@index')->name('admin.dashboard.index');

            Route::get('template', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@template')->name('admin.dashboard.template');

            // API routes
            Route::group([
                'prefix'    => 'api',
            ], function () {
                Route::group([
                    'prefix'    => 'dashboard',
                ], function () {
                    Route::get('/', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@getCardData')->name('admin.api.dashboard.card.index');

                    Route::get('/cards', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@getCards')->name('admin.api.dashboard.cards.index');

                    Route::post('/cards', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@updateCards')->name('admin.api.dashboard.cards.update');
                });
            });

            // User Routes
            Route::group([
                'prefix'    => 'account',
                'namespace' => 'Webkul\Admin\Http\Controllers\User',
            ], function () {
                Route::get('', 'AccountController@edit')->name('admin.user.account.edit');

                Route::put('update', 'AccountController@update')->name('admin.user.account.update');
            });

            // Leads Routes
            Route::group([
                'prefix'    => 'quotes',
                'namespace' => 'Webkul\Admin\Http\Controllers\Quote',
            ], function () {
                Route::get('', 'QuoteController@index')->name('admin.quotes.index');

                Route::get('create/{id?}', 'QuoteController@create')->name('admin.quotes.create');

                Route::post('create', 'QuoteController@store')->name('admin.quotes.store');

                Route::get('edit/{id?}', 'QuoteController@edit')->name('admin.quotes.edit');

                Route::put('edit/{id}', 'QuoteController@update')->name('admin.quotes.update');

                Route::get('print/{id?}', 'QuoteController@print')->name('admin.quotes.print');

                Route::delete('{id}', 'QuoteController@destroy')->name('admin.quotes.delete');

                Route::put('mass-destroy', 'QuoteController@massDestroy')->name('admin.quotes.mass_delete');
            });

            Route::group([
                'prefix'    => 'activities',
                'namespace' => 'Webkul\Admin\Http\Controllers\Activity',
            ], function () {
                Route::get('', 'ActivityController@index')->name('admin.activities.index');

                Route::get('get', 'ActivityController@get')->name('admin.activities.get');

                Route::post('is-overlapping', 'ActivityController@checkIfOverlapping')->name('admin.activities.check_overlapping');

                Route::post('create', 'ActivityController@store')->name('admin.activities.store');

                Route::get('edit/{id?}', 'ActivityController@edit')->name('admin.activities.edit');

                Route::put('edit/{id?}', 'ActivityController@update')->name('admin.activities.update');

                Route::get('search-participants', 'ActivityController@searchParticipants')->name('admin.activities.search_participants');

                Route::post('file-upload', 'ActivityController@upload')->name('admin.activities.file_upload');

                Route::get('file-download/{id?}', 'ActivityController@download')->name('admin.activities.file_download');

                Route::delete('{id?}', 'ActivityController@destroy')->name('admin.activities.delete');

                Route::put('mass-update', 'ActivityController@massUpdate')->name('admin.activities.mass_update');

                Route::put('mass-destroy', 'ActivityController@massDestroy')->name('admin.activities.mass_delete');
            });

            Route::group([
                'prefix'    => 'mail',
                'namespace' => 'Webkul\Admin\Http\Controllers\Mail',
            ], function () {
                Route::post('create', 'EmailController@store')->name('admin.mail.store');

                Route::put('edit/{id?}', 'EmailController@update')->name('admin.mail.update');

                Route::get('attachment-download/{id?}', 'EmailController@download')->name('admin.mail.attachment_download');

                Route::get('{route?}', 'EmailController@index')->name('admin.mail.index');

                Route::get('{route?}/{id?}', 'EmailController@view')->name('admin.mail.view');

                Route::delete('{id?}', 'EmailController@destroy')->name('admin.mail.delete');

                Route::put('mass-update', 'EmailController@massUpdate')->name('admin.mail.mass_update');

                Route::put('mass-destroy', 'EmailController@massDestroy')->name('admin.mail.mass_delete');
            });

            // Contacts Routes
            Route::group([
                'prefix'    => 'settings',
                'namespace' => 'Webkul\Admin\Http\Controllers\Settings',
            ], function () {

                Route::get('', 'SettingController@index')->name('admin.settings.index');

                // Attributes Routes
                Route::prefix('attributes')->group(function () {
                    Route::get('', 'AttributeController@index')->name('admin.settings.attributes.index');

                    Route::get('create', 'AttributeController@create')->name('admin.settings.attributes.create');

                    Route::post('create', 'AttributeController@store')->name('admin.settings.attributes.store');

                    Route::get('edit/{id}', 'AttributeController@edit')->name('admin.settings.attributes.edit');

                    Route::put('edit/{id}', 'AttributeController@update')->name('admin.settings.attributes.update');

                    Route::get('lookup/{lookup?}', 'AttributeController@lookup')->name('admin.settings.attributes.lookup');

                    Route::get('lookup-entity/{lookup?}', 'AttributeController@lookupEntity')->name('admin.settings.attributes.lookup_entity');

                    Route::delete('{id}', 'AttributeController@destroy')->name('admin.settings.attributes.delete');

                    Route::put('mass-update', 'AttributeController@massUpdate')->name('admin.settings.attributes.mass_update');

                    Route::put('mass-destroy', 'AttributeController@massDestroy')->name('admin.settings.attributes.mass_delete');

                    Route::get('download', 'AttributeController@download')->name('admin.settings.attributes.download');
                });

                // Lead Pipelines Routes
                Route::prefix('pipelines')->group(function () {
                    Route::get('', 'PipelineController@index')->name('admin.settings.pipelines.index');

                    Route::get('create', 'PipelineController@create')->name('admin.settings.pipelines.create');

                    Route::post('create', 'PipelineController@store')->name('admin.settings.pipelines.store');

                    Route::get('edit/{id?}', 'PipelineController@edit')->name('admin.settings.pipelines.edit');

                    Route::put('edit/{id}', 'PipelineController@update')->name('admin.settings.pipelines.update');

                    Route::delete('{id}', 'PipelineController@destroy')->name('admin.settings.pipelines.delete');
                });

                // Lead Sources Routes
                Route::prefix('sources')->group(function () {
                    Route::get('', 'SourceController@index')->name('admin.settings.sources.index');

                    Route::post('create', 'SourceController@store')->name('admin.settings.sources.store');

                    Route::get('edit/{id?}', 'SourceController@edit')->name('admin.settings.sources.edit');

                    Route::put('edit/{id}', 'SourceController@update')->name('admin.settings.sources.update');

                    Route::delete('{id}', 'SourceController@destroy')->name('admin.settings.sources.delete');
                });

                // Email Templates Routes
                Route::prefix('email-templates')->group(function () {
                    Route::get('', 'EmailTemplateController@index')->name('admin.settings.email_templates.index');

                    Route::get('create', 'EmailTemplateController@create')->name('admin.settings.email_templates.create');

                    Route::post('create', 'EmailTemplateController@store')->name('admin.settings.email_templates.store');

                    Route::get('edit/{id?}', 'EmailTemplateController@edit')->name('admin.settings.email_templates.edit');

                    Route::put('edit/{id}', 'EmailTemplateController@update')->name('admin.settings.email_templates.update');

                    Route::delete('{id}', 'EmailTemplateController@destroy')->name('admin.settings.email_templates.delete');
                });

                // Warehouses Routes
                Route::prefix('warehouses')->group(function () {
                    Route::get('', 'WarehouseController@index')->name('admin.settings.warehouses.index');

                    Route::get('search', 'WarehouseController@search')->name('admin.settings.warehouses.search');

                    Route::get('{id}/products', 'WarehouseController@products')->name('admin.settings.warehouses.products.index');

                    Route::get('create', 'WarehouseController@create')->name('admin.settings.warehouses.create');

                    Route::post('create', 'WarehouseController@store')->name('admin.settings.warehouses.store');

                    Route::get('view/{id}', 'WarehouseController@view')->name('admin.settings.warehouses.view');

                    Route::get('edit/{id?}', 'WarehouseController@edit')->name('admin.settings.warehouses.edit');

                    Route::put('edit/{id}', 'WarehouseController@update')->name('admin.settings.warehouses.update');

                    Route::delete('{id}', 'WarehouseController@destroy')->name('admin.settings.warehouses.delete');
                });

                // Warehouses Locations Routes
                Route::prefix('locations')->group(function () {
                    Route::get('search', 'LocationController@search')->name('admin.settings.locations.search');

                    Route::post('create', 'LocationController@store')->name('admin.settings.locations.store');

                    Route::put('edit/{id}', 'LocationController@update')->name('admin.settings.locations.update');

                    Route::delete('{id}', 'LocationController@destroy')->name('admin.settings.locations.delete');
                });

                // Tags Routes
                Route::prefix('tags')->group(function () {
                    Route::get('', 'TagController@index')->name('admin.settings.tags.index');

                    Route::post('create', 'TagController@store')->name('admin.settings.tags.store');

                    Route::get('edit/{id?}', 'TagController@edit')->name('admin.settings.tags.edit');

                    Route::put('edit/{id}', 'TagController@update')->name('admin.settings.tags.update');

                    Route::get('search', 'TagController@search')->name('admin.settings.tags.search');

                    Route::delete('{id}', 'TagController@destroy')->name('admin.settings.tags.delete');

                    Route::put('mass-destroy', 'TagController@massDestroy')->name('admin.settings.tags.mass_delete');
                });
            });

            Route::controller(SavedFilterController::class)->prefix('datagrid/saved-filters')->group(function () {
                Route::post('', 'store')->name('admin.datagrid.saved_filters.store');

                Route::get('', 'get')->name('admin.datagrid.saved_filters.index');

                Route::put('{id}', 'update')->name('admin.datagrid.saved_filters.update');

                Route::delete('{id}', 'destroy')->name('admin.datagrid.saved_filters.destroy');
            });

            // Configuration Routes
            Route::group([
                'prefix'    => 'configuration',
                'namespace' => 'Webkul\Admin\Http\Controllers\Configuration',
            ], function () {
                Route::get('{slug?}', 'ConfigurationController@index')->name('admin.configuration.index');

                Route::post('{slug?}', 'ConfigurationController@store')->name('admin.configuration.index.store');

                Route::get('{slug}/{path}', 'ConfigurationController@download')->name('admin.configuration.download');
            });
        });
    });
});
