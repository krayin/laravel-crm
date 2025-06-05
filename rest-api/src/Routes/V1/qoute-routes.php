<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Quote\QuoteController;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'quotes'], function () {
    Route::controller(QuoteController::class)->group(function () {
        Route::get('', 'index');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::post('', 'store');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');

        Route::post('mass-destroy', 'massDestroy');

        Route::get('search', 'search');
    });
});
