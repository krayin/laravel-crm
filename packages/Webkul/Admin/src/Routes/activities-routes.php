<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Activity\ActivityController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Persons routes.
     */
    Route::controller(ActivityController::class)->prefix('activities')->group(function () {
        Route::get('', 'index')->name('admin.activities.index');

        Route::get('get', 'get')->name('admin.activities.get');

        Route::post('is-overlapping', 'checkIfOverlapping')->name('admin.activities.check_overlapping');

        Route::post('create', 'store')->name('admin.activities.store');

        Route::get('edit/{id?}', 'edit')->name('admin.activities.edit');

        Route::put('edit/{id?}', 'update')->name('admin.activities.update');

        Route::get('search-participants', 'searchParticipants')->name('admin.activities.search_participants');

        Route::post('file-upload', 'upload')->name('admin.activities.file_upload');

        Route::get('file-download/{id?}', 'download')->name('admin.activities.file_download');

        Route::delete('{id?}', 'destroy')->name('admin.activities.delete');

        Route::put('mass-update', 'massUpdate')->name('admin.activities.mass_update');

        Route::put('mass-destroy', 'massDestroy')->name('admin.activities.mass_delete');
    });
});
