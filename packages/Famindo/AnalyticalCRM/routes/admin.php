<?php

use Illuminate\Support\Facades\Route;
use Famindo\AnalyticalCRM\Http\Controllers\Admin\AprioriController;

Route::controller(AprioriController::class)->prefix('analytics/market-basket')->group(function () {
    Route::get('', 'index')->name('admin.analytics.market_basket.index');

    Route::post('run', 'run')->name('admin.analytics.market_basket.run');
});

