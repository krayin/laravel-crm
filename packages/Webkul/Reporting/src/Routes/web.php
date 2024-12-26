<?php

use Illuminate\Support\Facades\Route;
use Webkul\Reporting\Http\Controllers\ReportingController;

Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('reporting')->group(function () {
        Route::get('', [ReportingController::class, 'index'])->name('admin.reporting.index');
        Route::get('create', [ReportingController::class, 'create'])->name('admin.reporting.create');
        Route::get('view/{id}', [ReportingController::class, 'view'])->name('admin.reporting.view');
        Route::post('store', [ReportingController::class, 'store'])->name('admin.reporting.store');
        Route::get('edit/{id}', [ReportingController::class, 'edit'])->name('admin.reporting.edit');
        Route::put('update/{id}', [ReportingController::class, 'update'])->name('admin.reporting.update');
        Route::delete('delete/{id}', [ReportingController::class, 'destroy'])->name('admin.reporting.delete');
        Route::post('mass_delete', [ReportingController::class, 'massDestroy'])->name('admin.reporting.mass_delete');
    });
});
