<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\TinyMCEController;

/**
 * Settings routes.
 */
Route::group(['middleware' => ['admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Tinymce file upload handler.
     */
    Route::post('tinymce/upload', [TinyMCEController::class, 'upload'])->name('admin.tinymce.upload');
});
