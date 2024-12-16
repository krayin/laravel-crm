<?php

use Illuminate\Support\Facades\Route;
use Webkul\Approval\Http\Controllers\ApprovalController;



Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('approval')->group(function () {
        Route::get('', [ApprovalController::class, 'index'])->name('admin.approval.index');
        Route::get('create', [ApprovalController::class, 'create'])->name('admin.approval.create');
        Route::post('store', [ApprovalController::class, 'store'])->name('admin.approval.store');
        Route::get('edit/{id}', [ApprovalController::class, 'edit'])->name('admin.approval.edit');
        Route::put('update/{id}', [ApprovalController::class, 'update'])->name('admin.approval.update');
        Route::delete('delete/{id}', [ApprovalController::class, 'destroy'])->name('admin.approval.delete');
        Route::post('mass_delete', [ApprovalController::class, 'massDestroy'])->name('admin.approval.mass_delete');
    });
});
