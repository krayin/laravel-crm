<?php

use Illuminate\Support\Facades\Route;
use Webkul\Order\Http\Controllers\OrderController;



Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('order')->group(function () {
        Route::get('', [OrderController::class, 'index'])->name('admin.order.index');
        Route::get('create', [OrderController::class, 'create'])->name('admin.order.create');
        Route::post('store', [OrderController::class, 'store'])->name('admin.order.store');
        Route::get('edit/{id}', [OrderController::class, 'edit'])->name('admin.order.edit');
        Route::put('update/{id}', [OrderController::class, 'update'])->name('admin.order.update');
        Route::delete('delete/{id}', [OrderController::class, 'destroy'])->name('admin.order.delete');
        Route::post('mass_delete', [OrderController::class, 'massDestroy'])->name('admin.order.mass_delete');
    });
});
