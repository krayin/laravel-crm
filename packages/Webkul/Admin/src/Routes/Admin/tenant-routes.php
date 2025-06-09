<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\TenantSwitchController;

Route::get('/tenants', [TenantSwitchController::class, 'index'])->name('admin.tenants.index');

Route::post('/tenant/switch', [TenantSwitchController::class, 'switch'])->name('admin.tenant.switch');
