<?php

use Illuminate\Support\Facades\Route;
use SuiteZap\LawFirm\Http\Controllers\Admin\ProcessoController;

Route::middleware(['web', 'admin_locale', 'user'])
    ->prefix(config('app.admin_path', 'admin') . '/juridico/processos')
    ->group(function () {
        Route::controller(ProcessoController::class)->group(function () {
            Route::get('', 'index')->name('admin.processos.index');
            Route::get('create', 'create')->name('admin.processos.create');
            Route::post('create', 'store')->name('admin.processos.store');
            Route::get('{id}', 'show')->name('admin.processos.show');
            Route::get('{id}/edit', 'edit')->name('admin.processos.edit');
            Route::put('{id}', 'update')->name('admin.processos.update');
            Route::delete('{id}', 'destroy')->name('admin.processos.destroy');
        });
    });