<?php

use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Http\Controllers\InventoryController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('inventory')->group(function () {
        Route::get('', [InventoryController::class, 'index'])->name('admin.inventory.index');
        Route::get('create', [InventoryController::class, 'create'])->name('admin.inventory.create');
        Route::get('view/{id}', [InventoryController::class, 'view'])->name('admin.inventory.view');
        Route::post('store', [InventoryController::class, 'store'])->name('admin.inventory.store');
        Route::get('edit/{id}', [InventoryController::class, 'edit'])->name('admin.inventory.edit');
        Route::put('update/{id}', [InventoryController::class, 'update'])->name('admin.inventory.update');
        Route::delete('delete/{id}', [InventoryController::class, 'destroy'])->name('admin.inventory.delete');
        Route::post('mass_delete', [InventoryController::class, 'massDestroy'])->name('admin.inventory.mass_delete');
        Route::get('export', [InventoryController::class, 'export'])->name('admin.inventory.export');
    });
});
