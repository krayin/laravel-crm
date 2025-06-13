<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Tenant\ApiTokenController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('api-token/{id}', [ApiTokenController::class, 'show']);
    Route::post('api-token/{id}', [ApiTokenController::class, 'store']);
    Route::delete('api-token/{id}', [ApiTokenController::class, 'destroy']);
});
