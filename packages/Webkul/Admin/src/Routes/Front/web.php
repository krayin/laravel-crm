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
        // Listagem de tenants
        Route::get('/', [SuperAdminController::class, 'tenantIndex'])
            ->name('index');

        // Formulário de cadastro de tenant
        Route::get('/create', [SuperAdminController::class, 'tenantCreate'])
            ->name('create');

        // Processa o cadastro de tenant
        Route::post('/', [SuperAdminController::class, 'tenantStore'])
            ->name('store');

        // Formulário de edição de tenant
        Route::get('/{id}/edit', [SuperAdminController::class, 'tenantEdit'])
            ->name('edit');

        // Processa o update de tenant
        Route::put('/{id}', [SuperAdminController::class, 'tenantUpdate'])
            ->name('update');

        // Processa a exclusão de tenant
        Route::delete('/{id}', [SuperAdminController::class, 'tenantDestroy'])
            ->name('destroy');
    });
