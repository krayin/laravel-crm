<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\User\AccountController;
use Webkul\RestApi\Http\Controllers\V1\User\AuthController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');

    Route::post('forgot-password', 'forgotPassword');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::delete('logout', [AuthController::class, 'logout']);

    Route::controller(AccountController::class)->group(function () {
        Route::get('get', 'get');

        Route::put('update', 'update');
    });
});
