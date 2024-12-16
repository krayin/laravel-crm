<?php

use Illuminate\Support\Facades\Route;
use Webkul\Client\Http\Controllers\ClientController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('client')->group(function () {
        Route::get('', [ClientController::class, 'index'])->name('admin.client.index');
        Route::get('create', [ClientController::class, 'create'])->name('admin.client.create');
        Route::post('store', [ClientController::class, 'store'])->name('admin.client.store');
        Route::get('edit/{id}', [ClientController::class, 'edit'])->name('admin.client.edit');
        Route::put('update/{id}', [ClientController::class, 'update'])->name('admin.client.update');
        Route::delete('delete/{id}', [ClientController::class, 'destroy'])->name('admin.client.delete');
        Route::post('mass_delete', [ClientController::class, 'massDestroy'])->name('admin.client.mass_delete');
    });
});
