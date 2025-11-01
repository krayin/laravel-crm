<?php

use Illuminate\Support\Facades\Route;
use Webkul\FeatureFlag\Http\Controllers\TestController;

Route::group([
    'prefix' => config('app.admin_path'),
    'middleware' => ['web', 'admin_locale', 'user']
], function () {
    
    Route::prefix('feature-flags')->group(function () {
        Route::get('/test', [TestController::class, 'index'])->name('admin.feature-flags.test');
        Route::post('/toggle/{feature}', [TestController::class, 'toggle'])->name('admin.feature-flags.toggle');
    });
    
});
