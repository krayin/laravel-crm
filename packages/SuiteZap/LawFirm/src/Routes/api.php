<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the LawFirm module.
| These routes are loaded by the LawFirmServiceProvider.
|
*/

Route::group([
    'prefix' => 'lawfirm',
    'middleware' => ['api'],
], function () {

    // API endpoints will be defined here
    // Example:
    // Route::get('/cases', [Api\CaseController::class, 'index']);
    // Route::post('/cases', [Api\CaseController::class, 'store']);
    // Route::get('/cases/{id}', [Api\CaseController::class, 'show']);
    // Route::put('/cases/{id}', [Api\CaseController::class, 'update']);
    // Route::delete('/cases/{id}', [Api\CaseController::class, 'destroy']);
});
