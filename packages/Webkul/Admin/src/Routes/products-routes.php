<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Products\ProductController;

Route::group(['middleware' => ['web', 'user', 'admin_locale'], 'prefix' => config('app.admin_path')], function () {
    /**
     * Products routes.
     */
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('', 'index')->name('admin.products.index');

        Route::get('create', 'create')->name('admin.products.create');

        Route::post('create', 'store')->name('admin.products.store');

        Route::get('view/{id}', 'view')->name('admin.products.view');

        Route::get('edit/{id}', 'edit')->name('admin.products.edit');

        Route::post('edit/{id}', 'update')->name('admin.products.update');

        Route::get('search', 'search')->name('admin.products.search');

        Route::get('{id}/warehouses', 'warehouses')->name('admin.products.warehouses');

        Route::post('{id}/inventories/{warehouseId?}', 'storeInventories')->name('admin.products.inventories.store');

        Route::delete('{id}', 'destroy')->name('admin.products.delete');

        Route::post('mass-destroy', 'massDestroy')->name('admin.products.mass_delete');
    });
});
