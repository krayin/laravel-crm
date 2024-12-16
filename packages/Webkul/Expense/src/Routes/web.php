<?php

use Illuminate\Support\Facades\Route;
use Webkul\Expense\Http\Controllers\ExpenseController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('expense')->group(function () {
        Route::get('', [ExpenseController::class, 'index'])->name('admin.expense.index');
        Route::get('create', [ExpenseController::class, 'create'])->name('admin.expense.create');
        Route::post('store', [ExpenseController::class, 'store'])->name('admin.expense.store');
        Route::get('edit/{id}', [ExpenseController::class, 'edit'])->name('admin.expense.edit');
        Route::put('update/{id}', [ExpenseController::class, 'update'])->name('admin.expense.update');
        Route::delete('delete/{id}', [ExpenseController::class, 'destroy'])->name('admin.expense.delete');
        Route::post('mass_delete', [ExpenseController::class, 'massDestroy'])->name('admin.expense.mass_delete');
    });
});

