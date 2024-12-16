<?php

use Illuminate\Support\Facades\Route;
use Webkul\AssetsAllocation\Http\Controllers\AssetsAllocationController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('AssetsAllocation')->group(function () {
        Route::get('', [AssetsAllocationController::class, 'index'])->name('admin.assetsAllocation.index');
        Route::get('create', [AssetsAllocationController::class, 'create'])->name('admin.assetsAllocation.create');
        Route::post('store', [AssetsAllocationController::class, 'store'])->name('admin.assetsAllocation.store');
        Route::get('edit/{id}', [AssetsAllocationController::class, 'edit'])->name('admin.assetsAllocation.edit');
        Route::put('update/{id}', [AssetsAllocationController::class, 'update'])->name('admin.assetsAllocation.update');
        Route::delete('delete/{id}', [AssetsAllocationController::class, 'destroy'])->name('admin.assetsAllocation.delete');
        Route::post('mass_delete', [AssetsAllocationController::class, 'massDestroy'])->name('admin.assetsAllocation.mass_delete');
    });
});
