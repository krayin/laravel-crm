<?php

use Illuminate\Support\Facades\Route;
use Webkul\Employee\Http\Controllers\EmployeeController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('employee')->group(function () {
        Route::get('', [EmployeeController::class, 'index'])->name('admin.employee.index');
        Route::get('create', [EmployeeController::class, 'create'])->name('admin.employee.create');
        Route::post('store', [EmployeeController::class, 'store'])->name('admin.employee.store');
        Route::get('edit/{id}', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
        Route::put('update/{id}', [EmployeeController::class, 'update'])->name('admin.employee.update');
        Route::delete('delete/{id}', [EmployeeController::class, 'destroy'])->name('admin.employee.delete');
        Route::post('mass_delete', [EmployeeController::class, 'massDestroy'])->name('admin.employee.mass_delete');
    });
});
