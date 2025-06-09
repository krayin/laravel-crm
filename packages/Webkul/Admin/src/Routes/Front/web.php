<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\TenantSwitchController;

/**
 * Home routes.
 */
Route::get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');

/**
 * Auto Login route.
 */

Route::get('/switch-login', [TenantSwitchController::class, 'handle_switch'])->name('admin.tenant.handle_switch_login');