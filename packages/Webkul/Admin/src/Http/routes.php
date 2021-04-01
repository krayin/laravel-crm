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
        Route::group(['middleware' => ['user']], function () {
            Route::get('logout', 'Webkul\Admin\Http\Controllers\User\SessionController@destroy')->name('admin.session.destroy');

            // Dashboard Route
            Route::get('dashboard', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@index')->name('admin.dashboard.index');

            Route::get('/api/datagrid', 'Webkul\Core\Http\Controllers\DatagridAPIController@index')
                ->name('admin.datagrid.api');

        // });
            // Leads Routes
            Route::prefix('leads')->group(function () {
                Route::get('', 'Webkul\Admin\Http\Controllers\Lead\LeadController@index')->name('admin.leads.index');
            });

            // Contacts Routes
            Route::prefix('contacts')->group(function () {
                Route::get('customers', 'Webkul\Admin\Http\Controllers\Contact\CustomerController@index')->name('admin.contacts.customers.index');

                Route::get('companies', 'Webkul\Admin\Http\Controllers\Contact\CompanyController@index')->name('admin.contacts.companies.index');
            });

            // Contacts Routes
            Route::prefix('settings')->group(function () {
                Route::get('users', 'Webkul\Admin\Http\Controllers\Setting\UserController@index')->name('admin.settings.users.index');

                Route::get('roles', 'Webkul\Admin\Http\Controllers\Setting\RoleController@index')->name('admin.settings.roles.index');
            });
        });

    });
});