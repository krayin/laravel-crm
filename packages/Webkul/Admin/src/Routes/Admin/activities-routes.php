<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Activity\ActivityController;

Route::controller(ActivityController::class)->prefix('activities')->name('admin.activities.')->group(function () {
    Route::get('', 'index')->name('index');

    Route::get('get', 'get')->name('get');

    Route::post('create', 'store')->name('store');

    Route::get('edit/{id}', 'edit')->name('edit');

    Route::put('edit/{id}', 'update')->name('update');

    Route::get('download/{id}', 'download')->name('file_download');

    Route::delete('{id}', 'destroy')->name('delete');

    Route::post('mass-update', 'massUpdate')->name('mass_update');

    Route::post('mass-destroy', 'massDestroy')->name('mass_delete');
});
