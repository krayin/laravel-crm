<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\User\SuperAdminController;

/**
 * Home routes.
 */
Route::get('/', [Controller::class, 'redirectToLogin'])->name('krayin.home');

Route::middleware(['web'])
     ->get('/super-admin', [SuperAdminController::class, 'index'])
     ->name('superAdmin.index');
