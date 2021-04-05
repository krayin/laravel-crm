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

            Route::get('/api/datagrid', 'Webkul\Core\Http\Controllers\DatagridAPIController@index')->name('admin.datagrid.api');

            // Leads Routes
            Route::prefix('leads')->group(function () {
                Route::get('', 'Webkul\Admin\Http\Controllers\Lead\LeadController@index')->name('admin.leads.index');
            });

            // Contacts Routes
            Route::prefix('contacts')->group(function () {
                // Customers Routes
                Route::prefix('customers')->group(function () {
                    Route::get('', 'Webkul\Admin\Http\Controllers\Contact\CustomerController@index')->name('admin.contacts.customers.index');
                });

                // Companies Routes
                Route::prefix('companies')->group(function () {
                    Route::get('', 'Webkul\Admin\Http\Controllers\Contact\CompanyController@index')->name('admin.contacts.companies.index');
                });
            });

            // Contacts Routes
            Route::prefix('settings')->group(function () {
                // Users Routes
                Route::prefix('users')->group(function () {
                    Route::get('', 'Webkul\Admin\Http\Controllers\Setting\UserController@index')->name('admin.settings.users.index');
                });

                // Roles Routes
                Route::prefix('roles')->group(function () {
                    Route::get('', 'Webkul\Admin\Http\Controllers\Setting\RoleController@index')->name('admin.settings.roles.index');
                });

                // Attributes Routes
                Route::prefix('attributes')->group(function () {
                    Route::get('', 'Webkul\Admin\Http\Controllers\Setting\AttributeController@index')->name('admin.settings.attributes.index');

                    Route::get('create', 'Webkul\Admin\Http\Controllers\Setting\AttributeController@create')->name('admin.settings.attributes.create');

                    Route::post('create', 'Webkul\Admin\Http\Controllers\Setting\AttributeController@store')->name('admin.settings.attributes.store');

                    Route::get('edit/{id}', 'Webkul\Admin\Http\Controllers\Setting\AttributeController@edit')->name('admin.settings.attributes.edit');

                    Route::put('edit/{id}', 'Webkul\Admin\Http\Controllers\Setting\AttributeController@update')->name('admin.settings.attributes.update');
                });
            });
        });

    });
});