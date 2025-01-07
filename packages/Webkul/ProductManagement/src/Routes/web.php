<?php

use Illuminate\Support\Facades\Route;
use Webkul\ProductManagement\Http\Controllers\ProductManagementController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('productmanagement')->group(function () {
        Route::get('', [ProductManagementController::class, 'index'])->name('admin.productmanagement.index');
        Route::get('create', [ProductManagementController::class, 'create'])->name('admin.productmanagement.create');
        Route::get('view/{id}', [ProductManagementController::class, 'view'])->name('admin.productmanagement.view');
        Route::post('store', [ProductManagementController::class, 'store'])->name('admin.productmanagement.store');
        Route::get('edit/{id}', [ProductManagementController::class, 'edit'])->name('admin.productmanagement.edit');
        Route::put('update/{id}', [ProductManagementController::class, 'update'])->name('admin.productmanagement.update');
        Route::delete('delete/{id}', [ProductManagementController::class, 'destroy'])->name('admin.productmanagement.delete');
        Route::post('mass_delete', [ProductManagementController::class, 'massDestroy'])->name('admin.productmanagement.mass_delete');
    });
});
