<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Mail\EmailController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['web', 'user', 'admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Email routes.
     */
    Route::controller(EmailController::class)->prefix('mail')->group(function () {
        Route::post('create', 'store')->name('admin.mail.store');

        Route::put('edit/{id}', 'update')->name('admin.mail.update');

        Route::get('attachment-download/{id?}', 'download')->name('admin.mail.attachment_download');

        Route::get('{route?}', 'index')->name('admin.mail.index');

        Route::get('{route}/{id}', 'view')->name('admin.mail.view');

        Route::delete('{id}', 'destroy')->name('admin.mail.delete');

        Route::put('mass-update', 'massUpdate')->name('admin.mail.mass_update');

        Route::put('mass-destroy', 'massDestroy')->name('admin.mail.mass_delete');
    });
});
