<?php

use Illuminate\Support\Facades\Route;
use Webkul\Transaction\Http\Controllers\TransactionController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('transaction')->group(function () {
        Route::get('', [TransactionController::class, 'index'])->name('admin.transaction.index');
        Route::get('create', [TransactionController::class, 'create'])->name('admin.transaction.create');
        Route::get('view/{id}', [TransactionController::class, 'view'])->name('admin.transaction.view');
        Route::post('store', [TransactionController::class, 'store'])->name('admin.transaction.store');
        Route::get('edit/{id}', [TransactionController::class, 'edit'])->name('admin.transaction.edit');
        Route::put('update/{id}', [TransactionController::class, 'update'])->name('admin.transaction.update');
        Route::delete('delete/{id}', [TransactionController::class, 'destroy'])->name('admin.transaction.delete');
        Route::post('mass_delete', [TransactionController::class, 'massDestroy'])->name('admin.transaction.mass_delete');
    });
});
