<?php

use Illuminate\Support\Facades\Route;
use Webkul\AssetManagement\Http\Controllers\AssetManagementController;
use Webkul\AssetManagement\Http\Controllers\WarehouseController;
use Webkul\AssetManagement\Http\Controllers\AssetUtilizationController;
use Webkul\AssetManagement\Http\Controllers\InstrumentController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){

    Route::get('asset-management', [AssetManagementController::class, 'index'])->name('admin.assetmanagement.index');


    Route::prefix('instrument')->group(function () {

        Route::get('', [InstrumentController::class, 'index'])->name('admin.instrument.index');
        Route::get('create', [InstrumentController::class, 'create'])->name('admin.instrument.create');
        Route::post('store', [InstrumentController::class, 'store'])->name('admin.instrument.store');
        Route::get('edit/{id}', [InstrumentController::class, 'edit'])->name('admin.instrument.edit');
        Route::put('update/{id}', [InstrumentController::class, 'update'])->name('admin.instrument.update');
        Route::delete('delete/{id}', [InstrumentController::class, 'destroy'])->name('admin.instrument.delete');
        Route::post('mass_delete', [InstrumentController::class, 'massDestroy'])->name('admin.instrument.mass_delete');
    });
    Route::prefix('warehouse')->group(function () {
        Route::get('', [WarehouseController::class, 'index'])->name('admin.warehouse.index');
        Route::get('create', [WarehouseController::class, 'create'])->name('admin.warehouse.create');
        Route::post('store', [WarehouseController::class, 'store'])->name('admin.warehouse.store');
        Route::get('edit/{id}', [WarehouseController::class, 'edit'])->name('admin.warehouse.edit');
        Route::put('update/{id}', [WarehouseController::class, 'update'])->name('admin.warehouse.update');
        Route::delete('delete/{id}', [WarehouseController::class, 'destroy'])->name('admin.warehouse.delete');
        Route::post('mass_delete', [WarehouseController::class, 'massDestroy'])->name('admin.warehouse.mass_delete');
    });
    Route::prefix('assetUtilization')->group(function () {
        Route::get('', [AssetUtilizationController::class, 'index'])->name('admin.assetUtilization.index');
        Route::get('create', [AssetUtilizationController::class, 'create'])->name('admin.assetUtilization.create');
        Route::post('store', [AssetUtilizationController::class, 'store'])->name('admin.assetUtilization.store');
        Route::get('edit/{id}', [AssetUtilizationController::class, 'edit'])->name('admin.assetUtilization.edit');
        Route::put('update/{id}', [AssetUtilizationController::class, 'update'])->name('admin.assetUtilization.update');
        Route::delete('delete/{id}', [AssetUtilizationController::class, 'destroy'])->name('admin.assetUtilization.delete');
        Route::post('mass_delete', [AssetUtilizationController::class, 'massDestroy'])->name('admin.assetUtilization.mass_delete');
    });
});
