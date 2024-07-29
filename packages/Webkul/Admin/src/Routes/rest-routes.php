<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\TinyMCEController;
use Webkul\Admin\Http\Controllers\User\AccountController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Tinymce file upload handler.
     */
    Route::post('tinymce/upload', [TinyMCEController::class, 'upload'])->name('admin.tinymce.upload');

    /**
     * User profile routes.
     */
    Route::controller(AccountController::class)->prefix('account')->group(function () {
        Route::get('', 'edit')->name('admin.user.account.edit');

        Route::put('update', 'update')->name('admin.user.account.update');
    });
});
