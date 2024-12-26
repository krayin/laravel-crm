<?php

use Illuminate\Support\Facades\Route;
use Webkul\Announcement\Http\Controllers\AnnouncementController;

Route::middleware(['web', 'admin_locale', 'user'])->prefix(config('app.admin_path'))->group(function () {
    Route::prefix('announcement')->group(function () {
        Route::get('', [AnnouncementController::class, 'index'])->name('admin.announcement.index');
        Route::get('create', [AnnouncementController::class, 'create'])->name('admin.announcement.create');
        Route::get('view/{id}', [AnnouncementController::class, 'view'])->name('admin.announcement.view');
        Route::post('store', [AnnouncementController::class, 'store'])->name('admin.announcement.store');
        Route::get('edit/{id}', [AnnouncementController::class, 'edit'])->name('admin.announcement.edit');
        Route::put('update/{id}', [AnnouncementController::class, 'update'])->name('admin.announcement.update');
        Route::delete('delete/{id}', [AnnouncementController::class, 'destroy'])->name('admin.announcement.delete');
        Route::post('mass_delete', [AnnouncementController::class, 'massDestroy'])->name('admin.announcement.mass_delete');
    });
});
