<?php

use Illuminate\Support\Facades\Route;
use Webkul\Invoice\Http\Controllers\InvoiceController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('invoice')->group(function () {
        Route::get('', [InvoiceController::class, 'index'])->name('admin.invoice.index');
        Route::get('create', [InvoiceController::class, 'create'])->name('admin.invoice.create');
        Route::post('store', [InvoiceController::class, 'store'])->name('admin.invoice.store');
        Route::get('edit/{id}', [InvoiceController::class, 'edit'])->name('admin.invoice.edit');
        Route::put('update/{id}', [InvoiceController::class, 'update'])->name('admin.invoice.update');
        Route::delete('delete/{id}', [InvoiceController::class, 'destroy'])->name('admin.invoice.delete');
        Route::post('mass_delete', [InvoiceController::class, 'massDestroy'])->name('admin.invoice.mass_delete');
    });
});
