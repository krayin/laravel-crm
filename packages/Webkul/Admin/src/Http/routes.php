<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix(config('app.admin_path'))->group(function () {

        Route::get('/', 'Webkul\Admin\Http\Controllers\Controller@redirectToLogin');

        // Login Routes
        Route::get('login', 'Webkul\Admin\Http\Controllers\User\SessionController@create')->name('admin.session.create');

        //login post route to admin auth controller
        Route::post('login', 'Webkul\Admin\Http\Controllers\User\SessionController@store')->name('admin.session.store');

        // Forget Password Routes
        Route::get('forgot-password', 'Webkul\Admin\Http\Controllers\User\ForgotPasswordController@create')->name('admin.forgot_password.create');

        Route::post('forgot-password', 'Webkul\Admin\Http\Controllers\User\ForgotPasswordController@store')->name('admin.forgot_password.store');

        // Reset Password Routes
        Route::get('reset-password/{token}', 'Webkul\Admin\Http\Controllers\User\ResetPasswordController@create')->name('admin.reset_password.create');

        Route::post('reset-password', 'Webkul\Admin\Http\Controllers\User\ResetPasswordController@store')->name('admin.reset_password.store');


        // Admin Routes
        // Route::group(['middleware' => ['admin']], function () {
            Route::get('logout', 'Webkul\Admin\Http\Controllers\User\SessionController@destroy')->name('admin.session.destroy');

            // Dashboard Route
            Route::get('dashboard', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@index')->name('admin.dashboard.index');
        // });

    });
});