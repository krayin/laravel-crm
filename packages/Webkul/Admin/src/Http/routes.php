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

            // Settings Routes
            Route::group([
                'prefix'    => 'settings',
                'namespace' => 'Webkul\Admin\Http\Controllers\Settings',
            ], function () {

                Route::get('', 'SettingController@index')->name('admin.settings.index');

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
