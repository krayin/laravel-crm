<?php

use Illuminate\Support\Facades\Route;
use Webkul\PriceFinder\Http\Controllers\PriceFinderController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('priceFinder')->group(function () {
        Route::get('', [PriceFinderController::class, 'index'])->name('admin.pricefinder.index');
        Route::get('create', [PriceFinderController::class, 'create'])->name('admin.pricefinder.create');
        Route::get('view/{id}', [PriceFinderController::class, 'view'])->name('admin.pricefinder.view');
        Route::post('store', [PriceFinderController::class, 'store'])->name('admin.pricefinder.store');
        Route::get('edit/{id}', [PriceFinderController::class, 'edit'])->name('admin.pricefinder.edit');
        Route::put('update/{id}', [PriceFinderController::class, 'update'])->name('admin.pricefinder.update');
        Route::delete('delete/{id}', [PriceFinderController::class, 'destroy'])->name('admin.pricefinder.delete');
        Route::post('mass_delete', [PriceFinderController::class, 'massDestroy'])->name('admin.pricefinder.mass_delete');
    });
});
