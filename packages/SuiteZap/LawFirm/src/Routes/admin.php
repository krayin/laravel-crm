<?php

use Illuminate\Support\Facades\Route;
use SuiteZap\LawFirm\Http\Controllers\Admin\ProcessoController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for the LawFirm module.
| These routes are loaded by the LawFirmServiceProvider.
|
*/

Route::group([
    'prefix' => 'admin/juridico',
    'middleware' => ['web', 'user'],
], function () {

    // Processos Routes
    Route::controller(ProcessoController::class)->prefix('processos')->group(function () {
        Route::get('', 'index')->name('admin.processos.index');
        Route::get('create', 'create')->name('admin.processos.create');
        Route::post('create', 'store')->name('admin.processos.store');
        Route::get('{id}', 'show')->name('admin.processos.show');
        Route::get('{id}/edit', 'edit')->name('admin.processos.edit');
        Route::put('{id}', 'update')->name('admin.processos.update');
        Route::delete('{id}', 'destroy')->name('admin.processos.destroy');
    });
});

// Legacy route (keeping for backward compatibility)
Route::group([
    'prefix' => 'admin/lawfirm',
    'middleware' => ['web', 'admin'],
], function () {

    // Dashboard
    Route::get('/', function () {
        return view('lawfirm::admin.index');
    })->name('admin.lawfirm.index');

    // Route::get('/cases', [CaseController::class, 'index'])->name('admin.lawfirm.cases.index');
    // Route::get('/cases/create', [CaseController::class, 'create'])->name('admin.lawfirm.cases.create');
    // Route::post('/cases', [CaseController::class, 'store'])->name('admin.lawfirm.cases.store');
    // Route::get('/cases/{id}', [CaseController::class, 'show'])->name('admin.lawfirm.cases.show');
    // Route::get('/cases/{id}/edit', [CaseController::class, 'edit'])->name('admin.lawfirm.cases.edit');
    // Route::put('/cases/{id}', [CaseController::class, 'update'])->name('admin.lawfirm.cases.update');
    // Route::delete('/cases/{id}', [CaseController::class, 'destroy'])->name('admin.lawfirm.cases.destroy');

    // Hearings Routes (Audiências)
    // Route::resource('hearings', HearingController::class)->names('admin.lawfirm.hearings');

    // Documents Routes (Documentos)
    // Route::resource('documents', DocumentController::class)->names('admin.lawfirm.documents');
});
