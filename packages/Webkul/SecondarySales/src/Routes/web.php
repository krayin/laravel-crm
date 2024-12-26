<?php

use Illuminate\Support\Facades\Route;
use Webkul\SecondarySales\Http\Controllers\SecondarySalesController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('secondarySales')->group(function () {
        Route::get('', [SecondarySalesController::class, 'index'])->name('admin.secondarysales.index');

        Route::get('create', [SecondarySalesController::class, 'create'])->name('admin.secondarysales.create');

        Route::post('store', [SecondarySalesController::class, 'store'])->name('admin.secondarysales.store');

        Route::get('edit/{id}', [SecondarySalesController::class, 'edit'])->name('admin.secondarysales.edit');

        Route::put('update/{id}', [SecondarySalesController::class, 'update'])->name('admin.secondarysales.update');

        Route::delete('delete/{id}', [SecondarySalesController::class, 'destroy'])->name('admin.secondarysales.delete');

        Route::post('mass_delete', [SecondarySalesController::class, 'massDestroy'])->name('admin.secondarysales.mass_delete');
        
    });
});
