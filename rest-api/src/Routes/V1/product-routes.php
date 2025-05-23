<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Product\ProductController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('', 'index');

        Route::post('', 'store');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::put('{id}', 'update');

        Route::get('search', 'search');

        Route::get('{id}/warehouses', 'warehouses');

        Route::post('{id}/inventories/{warehouseId?}', 'storeInventories');

        Route::delete('{id}', 'destroy');

        Route::post('mass-destroy', 'massDestroy');
    });
});
