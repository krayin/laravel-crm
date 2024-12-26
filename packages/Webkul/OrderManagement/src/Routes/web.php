<?php

use Illuminate\Support\Facades\Route;
use Webkul\OrderManagement\Http\Controllers\OrderManagementController;

// Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
//     Route::prefix('budget')->group(function () {
//         Route::get('', [BudgetController::class, 'index'])->name('admin.budget.index');
//         Route::get('create', [BudgetController::class, 'create'])->name('admin.budget.create');
//         Route::get('view/{id}', [BudgetController::class, 'view'])->name('admin.budget.view');
//         Route::post('store', [BudgetController::class, 'store'])->name('admin.budget.store');
//         Route::get('edit/{id}', [BudgetController::class, 'edit'])->name('admin.budget.edit');
//         Route::put('update/{id}', [BudgetController::class, 'update'])->name('admin.budget.update');
//         Route::delete('delete/{id}', [BudgetController::class, 'destroy'])->name('admin.budget.delete');
//         Route::post('mass_delete', [BudgetController::class, 'massDestroy'])->name('admin.budget.mass_delete');
//     });
// });


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('orderManagement')->group(function () {
        Route::get('', [OrderManagementController::class, 'index'])->name('admin.ordermanagement.index');
    });

    Route::prefix('createOrder')->group(function () {
        Route::get('', [OrderManagementController::class, 'create'])->name('admin.ordermanagement.create');
    });
});
