<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\DataGridController;
use Webkul\Admin\Http\Controllers\TinyMCEController;
use Webkul\Admin\Http\Controllers\User\AccountController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['web', 'user', 'admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Datagrid lookup routes.
     */
    Route::get('datagrid/look-up', [DataGridController::class, 'lookUp'])->name('admin.datagrid.look_up');

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
