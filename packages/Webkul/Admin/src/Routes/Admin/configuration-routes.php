<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Configuration\ConfigurationController;

Route::controller(ConfigurationController::class)->prefix('configuration')->group(function () {
    Route::get('search', 'search')->name('admin.configuration.search');

    Route::prefix('{slug?}/{slug2?}')->group(function () {
        Route::get('', 'index')->name('admin.configuration.index');

        Route::post('', 'store')->name('admin.configuration.store');

        Route::get('{path}', 'download')->name('admin.configuration.download');
    });
});
