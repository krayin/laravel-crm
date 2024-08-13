<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Quote\QuoteController;

Route::group(['middleware' => ['web', 'user', 'admin_locale'], 'prefix' => config('app.admin_path')], function () {
    Route::controller(QuoteController::class)->prefix('quotes')->group(function () {
        Route::get('', 'index')->name('admin.quotes.index');

        Route::get('create/{id?}', 'create')->name('admin.quotes.create');

        Route::post('create', 'store')->name('admin.quotes.store');

        Route::get('edit/{id?}', 'edit')->name('admin.quotes.edit');

        Route::put('edit/{id}', 'update')->name('admin.quotes.update');

        Route::get('print/{id?}', 'print')->name('admin.quotes.print');

        Route::delete('{id}', 'destroy')->name('admin.quotes.delete');

        Route::post('mass-destroy', 'massDestroy')->name('admin.quotes.mass_delete');
    });
});
