<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Activity\ActivityController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('activities', [ActivityController::class, 'index']);

    Route::get('activities/{id}', [ActivityController::class, 'show']);

    Route::post('activities/is-overlapping', [ActivityController::class, 'checkIfOverlapping']);

    Route::post('activities', [ActivityController::class, 'store']);

    Route::put('activities/{id?}', [ActivityController::class, 'update']);

    Route::post('activities/file-upload', [ActivityController::class, 'upload']);

    Route::get('activities/file-download/{id?}', [ActivityController::class, 'download']);

    Route::delete('activities/{id?}', [ActivityController::class, 'destroy']);

    Route::post('activities/mass-update', [ActivityController::class, 'massUpdate']);

    Route::post('activities/mass-destroy', [ActivityController::class, 'massDestroy']);
});
