<?php

use Illuminate\Support\Facades\Route;
use Webkul\Consignment\Http\Controllers\ConsignmentController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('consignment')->group(function () {
        Route::get('', [ConsignmentController::class, 'index'])->name('admin.consignment.index');
        Route::get('create', [ConsignmentController::class, 'create'])->name('admin.consignment.create');
        Route::get('view/{id}', [ConsignmentController::class, 'view'])->name('admin.consignment.view');
        Route::post('store', [ConsignmentController::class, 'store'])->name('admin.consignment.store');
        Route::get('edit/{id}', [ConsignmentController::class, 'edit'])->name('admin.consignment.edit');
        Route::put('update/{id}', [ConsignmentController::class, 'update'])->name('admin.consignment.update');
        Route::delete('delete/{id}', [ConsignmentController::class, 'destroy'])->name('admin.consignment.delete');
        Route::post('mass_delete', [ConsignmentController::class, 'massDestroy'])->name('admin.consignment.mass_delete');
    });
});
