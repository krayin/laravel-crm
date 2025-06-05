<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Mail\EmailController;
use Webkul\RestApi\Http\Controllers\V1\Mail\TagController;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'mails'], function () {

    Route::controller(EmailController::class)->group(function () {
        Route::get('', 'index');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::post('', 'store');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');

        Route::post('mass-update', 'massUpdate');

        Route::post('mass-destroy', 'massDestroy');

        Route::get('attachment-download/{id?}', 'download');
    });

    Route::controller(TagController::class)->prefix('{id}/tags')->group(function () {
        Route::post('', 'attach')->name('admin.mail.tags.attach');

        Route::delete('', 'detach')->name('admin.mail.tags.detach');
    });
});
