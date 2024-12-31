<?php

use Illuminate\Support\Facades\Route;
use Webkul\RepositoryDetails\Http\Controllers\RepositoryDetailsController;


Route::middleware(['web','admin_locale','user'])->prefix(config('app.admin_path'))->group(function(){
    Route::prefix('repositoryDetails')->group(function () {
        Route::get('', [RepositoryDetailsController::class, 'index'])->name('admin.repositorydetails.index');

        Route::get('create', [RepositoryDetailsController::class, 'create'])->name('admin.repositorydetails.create');

        Route::post('store', [RepositoryDetailsController::class, 'store'])->name('admin.repositorydetails.store');

        Route::get('edit/{id}', [RepositoryDetailsController::class, 'edit'])->name('admin.repositorydetails.edit');

        Route::put('update/{id}', [RepositoryDetailsController::class, 'update'])->name('admin.repositorydetails.update');

        Route::delete('delete/{id}', [RepositoryDetailsController::class, 'destroy'])->name('admin.repositorydetails.delete');

        Route::post('mass_delete', [RepositoryDetailsController::class, 'massDestroy'])->name('admin.repositorydetails.mass_delete');
    });
});
