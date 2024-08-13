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

            // Settings Routes
            Route::group([
                'prefix'    => 'settings',
                'namespace' => 'Webkul\Admin\Http\Controllers\Settings',
            ], function () {

                Route::get('', 'SettingController@index')->name('admin.settings.index');

                // Email Templates Routes
                Route::prefix('email-templates')->group(function () {
                    Route::get('', 'EmailTemplateController@index')->name('admin.settings.email_templates.index');

                    Route::get('create', 'EmailTemplateController@create')->name('admin.settings.email_templates.create');

                    Route::post('create', 'EmailTemplateController@store')->name('admin.settings.email_templates.store');

                    Route::get('edit/{id?}', 'EmailTemplateController@edit')->name('admin.settings.email_templates.edit');

                    Route::put('edit/{id}', 'EmailTemplateController@update')->name('admin.settings.email_templates.update');

                    Route::delete('{id}', 'EmailTemplateController@destroy')->name('admin.settings.email_templates.delete');
                });

                // Warehouses Locations Routes
                Route::prefix('locations')->group(function () {
                    Route::get('search', 'LocationController@search')->name('admin.settings.locations.search');

                    Route::post('create', 'LocationController@store')->name('admin.settings.locations.store');

                    Route::put('edit/{id}', 'LocationController@update')->name('admin.settings.locations.update');

                    Route::delete('{id}', 'LocationController@destroy')->name('admin.settings.locations.delete');
                });
            });

            Route::controller(SavedFilterController::class)->prefix('datagrid/saved-filters')->group(function () {
                Route::post('', 'store')->name('admin.datagrid.saved_filters.store');

                Route::get('', 'get')->name('admin.datagrid.saved_filters.index');

                Route::put('{id}', 'update')->name('admin.datagrid.saved_filters.update');

                Route::delete('{id}', 'destroy')->name('admin.datagrid.saved_filters.destroy');
            });

            // Configuration Routes
            // Route::group([
            //     'prefix'    => 'configuration',
            //     'namespace' => 'Webkul\Admin\Http\Controllers\Configuration',
            // ], function () {
            //     Route::get('{slug?}', 'ConfigurationController@index')->name('admin.configuration.index');

            //     Route::post('{slug?}', 'ConfigurationController@store')->name('admin.configuration.index.store');

            //     Route::get('{slug}/{path}', 'ConfigurationController@download')->name('admin.configuration.download');
            // });
        });
    });
});
