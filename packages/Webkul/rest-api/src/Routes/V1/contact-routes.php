<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Contact\Organizations\OrganizationController;
use Webkul\RestApi\Http\Controllers\V1\Contact\Persons\ActivityController;
use Webkul\RestApi\Http\Controllers\V1\Contact\Persons\PersonController;
use Webkul\RestApi\Http\Controllers\V1\Contact\Persons\TagController;

Route::group([
    'prefix'     => 'contacts',
    'middleware' => 'auth:sanctum',
], function () {
    /**
     * Person routes.
     */
    Route::controller(PersonController::class)->prefix('persons')->group(function () {
        Route::get('', 'index');

        Route::get('search', 'search')->where('query', '[A-Za-z0–9\-]+');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::post('', 'store');

        Route::put('{id}', 'update');

        Route::middleware(['throttle:100,60'])->delete('{id}', 'destroy');

        Route::post('mass-destroy', 'massDestroy');

        /**
         * Tag routes.
         */
        Route::controller(TagController::class)->prefix('{id}/tags')->group(function () {
            Route::post('', 'attach');

            Route::delete('', 'detach');
        });

        /**
         * Activity routes.
         */
        Route::controller(ActivityController::class)->prefix('{id}/activities')->group(function () {
            Route::get('', 'index');
        });
    });

    /**
     * Organization routes.
     */
    Route::controller(OrganizationController::class)->prefix('organizations')->group(function () {
        Route::get('', 'index');

        Route::get('{id}', 'show');

        Route::post('', 'store');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');

        Route::post('mass-destroy', 'massDestroy');
    });
});
