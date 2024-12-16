<?php

use Illuminate\Support\Facades\Route;
use Webkul\Asset\Http\Controllers\AssetController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('asset')->group(function () {
        Route::get('', [AssetController::class, 'index'])->name('admin.asset.index');
        Route::get('create', [AssetController::class, 'create'])->name('admin.asset.create');
        Route::post('store', [AssetController::class, 'store'])->name('admin.asset.store');
        Route::get('edit/{id}', [AssetController::class, 'edit'])->name('admin.asset.edit');
        Route::put('update/{id}', [AssetController::class, 'update'])->name('admin.asset.update');
        Route::delete('delete/{id}', [AssetController::class, 'destroy'])->name('admin.asset.delete');
        Route::post('mass_delete', [AssetController::class, 'massDestroy'])->name('admin.asset.mass_delete');
    });
});
