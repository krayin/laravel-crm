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
            Route::group([
                'prefix'    => 'settings',
                'namespace' => 'Webkul\Admin\Http\Controllers\Setting'
            ], function () {
                // Users Routes
                Route::prefix('users')->group(function () {
                    Route::get('', 'UserController@index')->name('admin.settings.users.index');
    
                    Route::get('create', 'UserController@create')->name('admin.settings.users.create');
    
                    Route::post('create', 'UserController@store')->name('admin.settings.users.store');

                    Route::get('edit/{id}', 'UserController@edit')->name('admin.settings.users.edit');

                    Route::put('edit/{id}', 'UserController@update')->name('admin.settings.users.update');

                    Route::delete('{id}', 'UserController@destroy')->name('admin.settings.users.delete');

                    Route::put('mass-update', 'UserController@massUpdate')->name('admin.settings.users.mass-update');

                    Route::delete('mass-destroy', 'UserController@massDestroy')->name('admin.settings.users.mass-delete');
                });

                // Roles Routes
                Route::prefix('roles')->group(function () {
                    Route::get('', 'RoleController@index')->name('admin.settings.roles.index');

                    Route::get('create', 'RoleController@create')->name('admin.settings.roles.create');

                    Route::post('create', 'RoleController@store')->name('admin.settings.roles.store');

                    Route::get('edit/{id}', 'RoleController@edit')->name('admin.settings.roles.edit');

                    Route::put('edit/{id}', 'RoleController@update')->name('admin.settings.roles.update');

                    Route::delete('{id}', 'RoleController@destroy')->name('admin.settings.roles.delete');
                });

                // Attributes Routes
                Route::prefix('attributes')->group(function () {
                    Route::get('', 'AttributeController@index')->name('admin.settings.attributes.index');

                    Route::get('create', 'AttributeController@create')->name('admin.settings.attributes.create');

                    Route::post('create', 'AttributeController@store')->name('admin.settings.attributes.store');

                    Route::get('edit/{id}', 'AttributeController@edit')->name('admin.settings.attributes.edit');

                    Route::put('edit/{id}', 'AttributeController@update')->name('admin.settings.attributes.update');
                });
            });
        });
    });
});