<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'Webkul\Admin\Http\Controllers\Controller@redirectToLogin')->name('krayin.home');
    
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

        Route::get('mail/inbound-parse', 'Webkul\Admin\Http\Controllers\Mail\EmailController@inboundParse')->name('admin.mail.inbound_parse');

        // Admin Routes
        Route::group(['middleware' => ['user']], function () {
            Route::get('logout', 'Webkul\Admin\Http\Controllers\User\SessionController@destroy')->name('admin.session.destroy');

            // Dashboard Route
            Route::get('dashboard', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@index')->name('admin.dashboard.index');

            Route::get('template', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@template')->name('admin.dashboard.template');

            // API routes
            Route::group([
                'prefix'    => 'api',
            ], function () {
                Route::get('/datagrid', 'Webkul\Core\Http\Controllers\DatagridAPIController@index')->name('admin.datagrid.api');

                Route::group([
                    'prefix'    => 'dashboard',
                ], function () {
                    Route::get('/', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@getCardData')->name('admin.api.dashboard.card.index');

                    Route::get('/cards', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@getCards')->name('admin.api.dashboard.cards.index');

                    Route::post('/cards', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@updateCards')->name('admin.api.dashboard.cards.update');
                });
            });

            // User Routes
            Route::group([
                'prefix'    => 'account',
                'namespace' => 'Webkul\Admin\Http\Controllers\User'
            ], function () {
                Route::get('', 'AccountController@edit')->name('admin.user.account.edit');
    
                Route::put('update', 'AccountController@update')->name('admin.user.account.update');
            });

            // Leads Routes
            Route::group([
                'prefix'    => 'leads',
                'namespace' => 'Webkul\Admin\Http\Controllers\Lead',
            ], function () {
                Route::get('', 'LeadController@index')->name('admin.leads.index');
    
                Route::post('create', 'LeadController@store')->name('admin.leads.store');

                Route::get('view/{id?}', 'LeadController@view')->name('admin.leads.view');
    
                Route::put('edit/{id}', 'LeadController@update')->name('admin.leads.update');

                Route::post('file-upload/{id}', 'LeadController@upload')->name('admin.leads.file_upload');

                Route::get('file-download/{id?}', 'LeadController@download')->name('admin.leads.file_download');

                Route::get('kanban-format', 'LeadController@fetchLeads')->name('admin.leads.kanban.index');

                Route::post('update-lead', 'LeadController@updateLeadStage')->name('admin.leads.kanban.update');

                Route::get('search', 'LeadController@search')->name('admin.leads.search');

                Route::delete('{id}', 'LeadController@destroy')->name('admin.leads.delete');

                Route::put('mass-update', 'LeadController@massUpdate')->name('admin.leads.mass-update');

                Route::put('mass-destroy', 'LeadController@massDestroy')->name('admin.leads.mass-delete');

                Route::post('tags/{id}', 'TagController@store')->name('admin.leads.tags.store');

                Route::delete('{lead_id}/{tag_id?}', 'TagController@detete')->name('admin.leads.tags.delete');
            });

            Route::group([
                'prefix'    => 'activities',
                'namespace' => 'Webkul\Admin\Http\Controllers\Activity',
            ], function () {
                Route::get('', 'ActivityController@index')->name('admin.activities.index');

                Route::post('create/{id}', 'ActivityController@store')->name('admin.activities.store');

                Route::put('edit/{id?}', 'ActivityController@update')->name('admin.activities.update');
            
                Route::delete('{id?}', 'ActivityController@destroy')->name('admin.activities.delete');
            });

            Route::group([
                'prefix'    => 'mail',
                'namespace' => 'Webkul\Admin\Http\Controllers\Mail',
            ], function () {
                Route::post('create', 'EmailController@store')->name('admin.mail.store');

                Route::put('edit/{id}', 'EmailController@update')->name('admin.mail.update');

                Route::get('attachment-download/{id?}', 'EmailController@download')->name('admin.mail.attachment_download');

                Route::get('{route?}', 'EmailController@index')->name('admin.mail.index');

                Route::get('{route?}/{id?}', 'EmailController@view')->name('admin.mail.view');

                Route::delete('{id?}', 'EmailController@destroy')->name('admin.mail.delete');

                Route::put('mass-destroy', 'EmailController@massDestroy')->name('admin.mail.mass-delete');
            });

            // Contacts Routes
            Route::group([
                'prefix'    => 'contacts',
                'namespace' => 'Webkul\Admin\Http\Controllers\Contact'
            ], function () {
                // Customers Routes
                Route::prefix('persons')->group(function () {
                    Route::get('', 'PersonController@index')->name('admin.contacts.persons.index');
    
                    Route::post('create', 'PersonController@store')->name('admin.contacts.persons.store');
    
                    Route::get('edit/{id?}', 'PersonController@edit')->name('admin.contacts.persons.edit');
    
                    Route::put('edit/{id}', 'PersonController@update')->name('admin.contacts.persons.update');

                    Route::get('search', 'PersonController@search')->name('admin.contacts.persons.search');

                    Route::delete('{id}', 'PersonController@destroy')->name('admin.contacts.persons.delete');

                    Route::put('mass-destroy', 'PersonController@massDestroy')->name('admin.contacts.persons.mass-delete');
                });

                // Companies Routes
                Route::prefix('organizations')->group(function () {
                    Route::get('', 'OrganizationController@index')->name('admin.contacts.organizations.index');
    
                    Route::post('create', 'OrganizationController@store')->name('admin.contacts.organizations.store');
    
                    Route::get('edit/{id?}', 'OrganizationController@edit')->name('admin.contacts.organizations.edit');
    
                    Route::put('edit/{id}', 'OrganizationController@update')->name('admin.contacts.organizations.update');

                    Route::delete('{id}', 'OrganizationController@destroy')->name('admin.contacts.organizations.delete');

                    Route::put('mass-destroy', 'OrganizationController@massDestroy')->name('admin.contacts.organizations.mass-delete');
                });
            });

            // Products Routes
            Route::group([
                'prefix'    => 'products',
                'namespace' => 'Webkul\Admin\Http\Controllers\Product'
            ], function () {
                Route::get('', 'ProductController@index')->name('admin.products.index');
    
                Route::post('create', 'ProductController@store')->name('admin.products.store');

                Route::get('edit/{id}', 'ProductController@edit')->name('admin.products.edit');

                Route::put('edit/{id}', 'ProductController@update')->name('admin.products.update');

                Route::get('search', 'ProductController@search')->name('admin.products.search');

                Route::delete('{id}', 'ProductController@destroy')->name('admin.products.delete');

                Route::put('mass-destroy', 'ProductController@massDestroy')->name('admin.products.mass-delete');
            });

            // Contacts Routes
            Route::group([
                'prefix'    => 'settings',
                'namespace' => 'Webkul\Admin\Http\Controllers\Setting'
            ], function () {
                // Roles Routes
                Route::prefix('roles')->group(function () {
                    Route::get('', 'RoleController@index')->name('admin.settings.roles.index');

                    Route::get('create', 'RoleController@create')->name('admin.settings.roles.create');

                    Route::post('create', 'RoleController@store')->name('admin.settings.roles.store');

                    Route::get('edit/{id}', 'RoleController@edit')->name('admin.settings.roles.edit');

                    Route::put('edit/{id}', 'RoleController@update')->name('admin.settings.roles.update');

                    Route::delete('{id}', 'RoleController@destroy')->name('admin.settings.roles.delete');
                });

                // Users Routes
                Route::prefix('users')->group(function () {
                    Route::get('', 'UserController@index')->name('admin.settings.users.index');
    
                    Route::get('create', 'UserController@create')->name('admin.settings.users.create');
    
                    Route::post('create', 'UserController@store')->name('admin.settings.users.store');

                    Route::get('edit/{id?}', 'UserController@edit')->name('admin.settings.users.edit');

                    Route::put('edit/{id}', 'UserController@update')->name('admin.settings.users.update');

                    Route::delete('{id}', 'UserController@destroy')->name('admin.settings.users.delete');

                    Route::put('mass-update', 'UserController@massUpdate')->name('admin.settings.users.mass-update');

                    Route::put('mass-destroy', 'UserController@massDestroy')->name('admin.settings.users.mass-delete');
                });

                // Attributes Routes
                Route::prefix('attributes')->group(function () {
                    Route::get('', 'AttributeController@index')->name('admin.settings.attributes.index');

                    Route::get('create', 'AttributeController@create')->name('admin.settings.attributes.create');

                    Route::post('create', 'AttributeController@store')->name('admin.settings.attributes.store');

                    Route::get('edit/{id}', 'AttributeController@edit')->name('admin.settings.attributes.edit');

                    Route::put('edit/{id}', 'AttributeController@update')->name('admin.settings.attributes.update');
                    
                    Route::get('lookup/{id?}', 'AttributeController@search')->name('admin.settings.attributes.lookup');

                    Route::delete('{id}', 'AttributeController@destroy')->name('admin.settings.attributes.delete');

                    Route::put('mass-update', 'AttributeController@massUpdate')->name('admin.settings.attributes.mass-update');

                    Route::put('mass-destroy', 'AttributeController@massDestroy')->name('admin.settings.attributes.mass-delete');

                    Route::get('download', 'AttributeController@download')->name('admin.settings.attributes.download');
                });

                // Lead Types Routes
                Route::prefix('types')->group(function () {
                    Route::get('', 'TypeController@index')->name('admin.settings.types.index');
    
                    Route::post('create', 'TypeController@store')->name('admin.settings.types.store');

                    Route::get('edit/{id?}', 'TypeController@edit')->name('admin.settings.types.edit');

                    Route::put('edit/{id}', 'TypeController@update')->name('admin.settings.types.update');

                    Route::delete('{id}', 'TypeController@destroy')->name('admin.settings.types.delete');
                });

                // Lead Sources Routes
                Route::prefix('sources')->group(function () {
                    Route::get('', 'SourceController@index')->name('admin.settings.sources.index');
    
                    Route::post('create', 'SourceController@store')->name('admin.settings.sources.store');

                    Route::get('edit/{id?}', 'SourceController@edit')->name('admin.settings.sources.edit');

                    Route::put('edit/{id}', 'SourceController@update')->name('admin.settings.sources.update');

                    Route::delete('{id}', 'SourceController@destroy')->name('admin.settings.sources.delete');
                });

                // Tags Routes
                Route::prefix('tags')->group(function () {
                    Route::post('create', 'TagController@store')->name('admin.settings.tags.store');

                    Route::get('search', 'TagController@search')->name('admin.settings.tags.search');
                });
            });

            // Configuration Routes
            Route::group([
                'prefix'    => 'configuration',
                'namespace' => 'Webkul\Admin\Http\Controllers\Configuration'
            ], function () {
                Route::get('{slug?}/{slug2?}', 'ConfigurationController@index')->name('admin.configuration.index');

                Route::post('{slug?}/{slug2?}', 'ConfigurationController@store')->name('admin.configuration.index.store');
            });
        });
    });
});