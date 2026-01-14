<?php

use Illuminate\Support\Facades\Route;
use SuiteZap\LawFirm\Http\Controllers\Api\ProcessApiController;
use SuiteZap\LawFirm\Http\Controllers\Api\DeadlineApiController;

/*
|--------------------------------------------------------------------------
| LawFirm API Routes
|--------------------------------------------------------------------------
|
| API Routes for LawFirm Package.
| Base Prefix: /api/lawfirm
| Middleware: api, auth:sanctum
|
*/

Route::group(['prefix' => 'api/lawfirm', 'middleware' => ['api', 'auth:sanctum']], function () {

    // ===========================================
    // Processes API
    // ===========================================
    Route::controller(ProcessApiController::class)->prefix('processes')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{id}', 'show');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

    // ===========================================
    // Deadlines API
    // ===========================================
    // ===========================================
    // Deadlines API
    // ===========================================
    Route::controller(DeadlineApiController::class)->prefix('deadlines')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{id}', 'show');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });

    // ===========================================
    // Document Checklist API
    // ===========================================
    Route::get('documents/{processId}', [SuiteZap\LawFirm\Http\Controllers\Api\DocumentChecklistApiController::class, 'index']);
    Route::put('documents/{id}', [SuiteZap\LawFirm\Http\Controllers\Api\DocumentChecklistApiController::class, 'update']);
    Route::post('documents/{id}/upload', [SuiteZap\LawFirm\Http\Controllers\Api\DocumentChecklistApiController::class, 'uploadFile']);

});
