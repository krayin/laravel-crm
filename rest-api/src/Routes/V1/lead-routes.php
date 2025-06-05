<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Lead\ActivityController;
use Webkul\RestApi\Http\Controllers\V1\Lead\EmailController;
use Webkul\RestApi\Http\Controllers\V1\Lead\LeadController;
use Webkul\RestApi\Http\Controllers\V1\Lead\QuoteController;
use Webkul\RestApi\Http\Controllers\V1\Lead\TagController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'leads'], function () {
    /**
     * Leads Routes.
     */
    Route::controller(LeadController::class)->group(function () {
        Route::get('', 'index');

        Route::get('{id}', 'show')->where('id', '[0-9]+');

        Route::post('', 'store');

        Route::put('{id}', 'update');

        Route::delete('{id}', 'destroy');

        Route::post('mass-update', 'massUpdate');

        Route::post('create-by-ai', 'createByAI');

        Route::post('mass-destroy', 'massDestroy');

        Route::put('attributes/edit/{id}', 'updateAttributes');

        Route::put('stage/edit/{id}', 'updateStage');

        Route::put('product/{lead_id}', 'addProduct');

        Route::delete('product/{lead_id}', 'removeProduct');

        Route::get('kanban/look-up', 'kanbanLookup')->name('admin.leads.kanban.look_up');

        Route::get('search', 'search');

        Route::get('get/{pipeline_id?}', 'get');
    });

    /**
     * Tags Routes.
     */
    Route::controller(TagController::class)->prefix('{id}/tags')->group(function () {
        Route::post('', 'attach');

        Route::delete('', 'detach');
    });

    /**
     * Email Routes.
     */
    Route::controller(EmailController::class)->prefix('{id}/emails')->group(function () {
        Route::post('', 'store');

        Route::delete('', 'detach');
    });

    Route::controller(ActivityController::class)->prefix('{id}/activities')->group(function () {
        Route::get('', 'index');
    });

    Route::controller(QuoteController::class)->prefix('{id}/quotes')->group(function () {
        Route::delete('{quote_id?}', 'delete');
    });
});
