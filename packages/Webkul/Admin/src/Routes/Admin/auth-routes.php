<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Controllers\User\ForgotPasswordController;
use Webkul\Admin\Http\Controllers\User\ResetPasswordController;
use Webkul\Admin\Http\Controllers\User\SessionController;

Route::middleware(['web'])->withoutMiddleware(['user'])->group(function () {
    /**
     * Redirect route.
     */
    Route::get('/', [Controller::class, 'redirectToLogin']);

    /**
     * Session routes.
     */
    Route::controller(SessionController::class)->group(function () {

        // var_dump(Auth::check(), Auth::user(), session()->all());

        Route::prefix('login')->group(function () {
            Route::get('', 'create')->name('admin.session.create');

            Route::post('', 'store')->name('admin.session.store');
        });

        Route::middleware(['user'])->group(function () {
            Route::delete('logout', 'destroy')->name('admin.session.destroy');
        });
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
