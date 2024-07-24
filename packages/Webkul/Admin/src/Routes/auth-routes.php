<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\User\ForgotPasswordController;
use Webkul\Admin\Http\Controllers\User\ResetPasswordController;
use Webkul\Admin\Http\Controllers\User\SessionController;

/**
 * Auth routes.
 */
Route::group(['prefix' => config('app.admin_url')], function () {
    /**
     * Redirect route.
     */
    Route::get('/', [Controller::class, 'redirectToLogin']);

    Route::controller(SessionController::class)->prefix('login')->group(function () {
        /**
         * Login routes.
         */
        Route::get('', 'create')->name('admin.session.create');

        /**
         * Login post route to admin auth controller.
         */
        Route::post('', 'store')->name('admin.session.store');
    });

    /**
     * Forgot password routes.
     */
    Route::controller(ForgotPasswordController::class)->prefix('forget-password')->group(function () {
        Route::get('', 'create')->name('admin.forgot_password.create');

        Route::post('', 'store')->name('admin.forgot_password.store');
    });

    /**
     * Reset password routes.
     */
    Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
        Route::get('{token}', 'create')->name('admin.reset_password.create');

        Route::post('', 'store')->name('admin.reset_password.store');
    });
});
