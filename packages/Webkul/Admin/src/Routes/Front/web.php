<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\User\SuperAdminController;
use Webkul\Admin\Http\Controllers\TenantSwitchController;

/**
 * Home routes.
 */
Route::get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');

/**
* Auto Login route.
*/
Route::get('/switch-login', [TenantSwitchController::class, 'handle_switch'])->name('admin.tenant.handle_switch_login');


Route::middleware(['web'])
    ->prefix('super-admin/tenants')
    ->name('superAdmin.tenants.')
    ->group(function () {
        // Listagem
        Route::get('/', [SuperAdminController::class, 'index'])
            ->name('index');

        // Formulário de edição
        Route::get('/{id}/edit', [SuperAdminController::class, 'edit'])
            ->name('edit');

        // Processa o update
        Route::put('/{id}', [SuperAdminController::class, 'update'])
            ->name('update');
            
        // Processa a exclusão
        Route::delete('/{id}', [SuperAdminController::class, 'destroy'])
            ->name('destroy');

        // Formulário de cadastro
        Route::get('/create', [SuperAdminController::class, 'create'])
            ->name('create');

        // Processa o cadastro
            Route::post('/', [SuperAdminController::class, 'store'])
            ->name('store');
    });