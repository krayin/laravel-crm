<?php

use Illuminate\Support\Facades\Route;
use Webkul\AssetManagement\Http\Controllers\AssetManagementController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('assetManagement')->group(function () {
        Route::get('', [AssetManagementController::class, 'index'])->name('admin.assetmanagement.index');

        Route::get('create', [AssetManagementController::class, 'create'])->name('admin.assetmanagement.create');

        Route::post('store', [AssetManagementController::class, 'store'])->name('admin.assetmanagement.store');

        Route::get('edit/{id}', [AssetManagementController::class, 'edit'])->name('admin.assetmanagement.edit');

        Route::put('update/{id}', [AssetManagementController::class, 'update'])->name('admin.assetmanagement.update');

        Route::delete('delete/{id}', [AssetManagementController::class, 'destroy'])->name('admin.assetmanagement.delete');

        Route::post('mass_delete', [AssetManagementController::class, 'massDestroy'])->name('admin.assetmanagement.mass_delete');
    });
});
