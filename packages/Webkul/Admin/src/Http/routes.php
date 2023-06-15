<?php

Route::group(['middleware' => ['web', 'admin_locale']], function () {
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

        Route::post('mail/inbound-parse', 'Webkul\Admin\Http\Controllers\Mail\EmailController@inboundParse')->name('admin.mail.inbound_parse');

        // Admin Routes
        Route::group(['middleware' => ['user']], function () {
            Route::delete('logout', 'Webkul\Admin\Http\Controllers\User\SessionController@destroy')->name('admin.session.destroy');

            // Dashboard Route
            Route::get('dashboard', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@index')->name('admin.dashboard.index');

            Route::get('template', 'Webkul\Admin\Http\Controllers\Admin\DashboardController@template')->name('admin.dashboard.template');

            // API routes
            Route::group([
                'prefix'    => 'api',
            ], function () {
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
                Route::get('create', 'LeadController@create')->name('admin.leads.create');

                Route::post('create', 'LeadController@store')->name('admin.leads.store');

                Route::get('view/{id?}', 'LeadController@view')->name('admin.leads.view');

                Route::put('edit/{id?}', 'LeadController@update')->name('admin.leads.update');

                Route::get('search', 'LeadController@search')->name('admin.leads.search');

                Route::delete('{id}', 'LeadController@destroy')->name('admin.leads.delete');

                Route::put('mass-update', 'LeadController@massUpdate')->name('admin.leads.mass_update');

                Route::put('mass-destroy', 'LeadController@massDestroy')->name('admin.leads.mass_delete');

                Route::post('tags/{id}', 'TagController@store')->name('admin.leads.tags.store');

                Route::delete('{lead_id}/{tag_id?}', 'TagController@delete')->name('admin.leads.tags.delete');

                Route::get('get/{pipeline_id?}', 'LeadController@get')->name('admin.leads.get');

                Route::get('{pipeline_id?}', 'LeadController@index')->name('admin.leads.index');

                Route::group([
                    'prefix'    => 'quotes',
                ], function () {
                    Route::delete('{lead_id}/{quote_id?}', 'QuoteController@delete')->name('admin.leads.quotes.delete');
                });
            });

            // Leads Routes
            Route::group([
                'prefix'    => 'quotes',
                'namespace' => 'Webkul\Admin\Http\Controllers\Quote',
            ], function () {
                Route::get('', 'QuoteController@index')->name('admin.quotes.index');

                Route::get('create/{id?}', 'QuoteController@create')->name('admin.quotes.create');

                Route::post('create', 'QuoteController@store')->name('admin.quotes.store');

                Route::get('edit/{id?}', 'QuoteController@edit')->name('admin.quotes.edit');

                Route::put('edit/{id}', 'QuoteController@update')->name('admin.quotes.update');

                Route::get('print/{id?}', 'QuoteController@print')->name('admin.quotes.print');

                Route::delete('{id}', 'QuoteController@destroy')->name('admin.quotes.delete');

                Route::put('mass-destroy', 'QuoteController@massDestroy')->name('admin.quotes.mass_delete');
            });

            Route::group([
                'prefix'    => 'activities',
                'namespace' => 'Webkul\Admin\Http\Controllers\Activity',
            ], function () {
                Route::get('', 'ActivityController@index')->name('admin.activities.index');

                Route::get('get', 'ActivityController@get')->name('admin.activities.get');

                Route::post('is-overlapping', 'ActivityController@checkIfOverlapping')->name('admin.activities.check_overlapping');

                Route::post('create', 'ActivityController@store')->name('admin.activities.store');

                Route::get('edit/{id?}', 'ActivityController@edit')->name('admin.activities.edit');

                Route::put('edit/{id?}', 'ActivityController@update')->name('admin.activities.update');

                Route::get('search-participants', 'ActivityController@searchParticipants')->name('admin.activities.search_participants');

                Route::post('file-upload', 'ActivityController@upload')->name('admin.activities.file_upload');

                Route::get('file-download/{id?}', 'ActivityController@download')->name('admin.activities.file_download');

                Route::delete('{id?}', 'ActivityController@destroy')->name('admin.activities.delete');

                Route::put('mass-update', 'ActivityController@massUpdate')->name('admin.activities.mass_update');

                Route::put('mass-destroy', 'ActivityController@massDestroy')->name('admin.activities.mass_delete');
            });

            Route::group([
                'prefix'    => 'mail',
                'namespace' => 'Webkul\Admin\Http\Controllers\Mail',
            ], function () {
                Route::post('create', 'EmailController@store')->name('admin.mail.store');

                Route::put('edit/{id?}', 'EmailController@update')->name('admin.mail.update');

                Route::get('attachment-download/{id?}', 'EmailController@download')->name('admin.mail.attachment_download');

                Route::get('{route?}', 'EmailController@index')->name('admin.mail.index');

                Route::get('{route?}/{id?}', 'EmailController@view')->name('admin.mail.view');

                Route::delete('{id?}', 'EmailController@destroy')->name('admin.mail.delete');

                Route::put('mass-update', 'EmailController@massUpdate')->name('admin.mail.mass_update');

                Route::put('mass-destroy', 'EmailController@massDestroy')->name('admin.mail.mass_delete');
            });

            // Contacts Routes
            Route::group([
                'prefix'    => 'contacts',
                'namespace' => 'Webkul\Admin\Http\Controllers\Contact'
            ], function () {
                // Customers Routes
                Route::prefix('persons')->group(function () {
                    Route::get('', 'PersonController@index')->name('admin.contacts.persons.index');

                    Route::get('create', 'PersonController@create')->name('admin.contacts.persons.create');

                    Route::post('create', 'PersonController@store')->name('admin.contacts.persons.store');

                    Route::get('edit/{id?}', 'PersonController@edit')->name('admin.contacts.persons.edit');

                    Route::put('edit/{id}', 'PersonController@update')->name('admin.contacts.persons.update');

                    Route::get('search', 'PersonController@search')->name('admin.contacts.persons.search');

                    Route::middleware(['throttle:100,60'])->delete('{id}', 'PersonController@destroy')->name('admin.contacts.persons.delete');

                    Route::put('mass-destroy', 'PersonController@massDestroy')->name('admin.contacts.persons.mass_delete');
                });

                // Companies Routes
                Route::prefix('organizations')->group(function () {
                    Route::get('', 'OrganizationController@index')->name('admin.contacts.organizations.index');

                    Route::get('create', 'OrganizationController@create')->name('admin.contacts.organizations.create');

                    Route::post('create', 'OrganizationController@store')->name('admin.contacts.organizations.store');

                    Route::get('edit/{id?}', 'OrganizationController@edit')->name('admin.contacts.organizations.edit');

                    Route::put('edit/{id}', 'OrganizationController@update')->name('admin.contacts.organizations.update');

                    Route::delete('{id}', 'OrganizationController@destroy')->name('admin.contacts.organizations.delete');

                    Route::put('mass-destroy', 'OrganizationController@massDestroy')->name('admin.contacts.organizations.mass_delete');
                });
            });

            // Products Routes
            Route::group([
                'prefix'    => 'products',
                'namespace' => 'Webkul\Admin\Http\Controllers\Product'
            ], function () {
                Route::get('', 'ProductController@index')->name('admin.products.index');

                Route::get('create', 'ProductController@create')->name('admin.products.create');

                Route::post('create', 'ProductController@store')->name('admin.products.store');

                Route::get('edit/{id}', 'ProductController@edit')->name('admin.products.edit');

                Route::put('edit/{id}', 'ProductController@update')->name('admin.products.update');

                Route::get('search', 'ProductController@search')->name('admin.products.search');

                Route::delete('{id}', 'ProductController@destroy')->name('admin.products.delete');

                Route::put('mass-destroy', 'ProductController@massDestroy')->name('admin.products.mass_delete');
            });

            // Contacts Routes
            Route::group([
                'prefix'    => 'settings',
                'namespace' => 'Webkul\Admin\Http\Controllers\Setting'
            ], function () {

                Route::get('', 'SettingController@index')->name('admin.settings.index');

                // Groups Routes
                Route::prefix('groups')->group(function () {
                    Route::get('', 'GroupController@index')->name('admin.settings.groups.index');

                    Route::get('create', 'GroupController@create')->name('admin.settings.groups.create');

                    Route::post('create', 'GroupController@store')->name('admin.settings.groups.store');

                    Route::get('edit/{id}', 'GroupController@edit')->name('admin.settings.groups.edit');

                    Route::put('edit/{id}', 'GroupController@update')->name('admin.settings.groups.update');

                    Route::delete('{id}', 'GroupController@destroy')->name('admin.settings.groups.delete');
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

                // Users Routes
                Route::prefix('users')->group(function () {
                    Route::get('', 'UserController@index')->name('admin.settings.users.index');

                    Route::get('create', 'UserController@create')->name('admin.settings.users.create');

                    Route::post('create', 'UserController@store')->name('admin.settings.users.store');

                    Route::get('edit/{id?}', 'UserController@edit')->name('admin.settings.users.edit');

                    Route::put('edit/{id}', 'UserController@update')->name('admin.settings.users.update');

                    Route::delete('{id}', 'UserController@destroy')->name('admin.settings.users.delete');

                    Route::put('mass-update', 'UserController@massUpdate')->name('admin.settings.users.mass_update');

                    Route::put('mass-destroy', 'UserController@massDestroy')->name('admin.settings.users.mass_delete');
                });

                // Attributes Routes
                Route::prefix('attributes')->group(function () {
                    Route::get('', 'AttributeController@index')->name('admin.settings.attributes.index');

                    Route::get('create', 'AttributeController@create')->name('admin.settings.attributes.create');

                    Route::post('create', 'AttributeController@store')->name('admin.settings.attributes.store');

                    Route::get('edit/{id}', 'AttributeController@edit')->name('admin.settings.attributes.edit');

                    Route::put('edit/{id}', 'AttributeController@update')->name('admin.settings.attributes.update');

                    Route::get('lookup/{lookup?}', 'AttributeController@lookup')->name('admin.settings.attributes.lookup');

                    Route::get('lookup-entity/{lookup?}', 'AttributeController@lookupEntity')->name('admin.settings.attributes.lookup_entity');

                    Route::delete('{id}', 'AttributeController@destroy')->name('admin.settings.attributes.delete');

                    Route::put('mass-update', 'AttributeController@massUpdate')->name('admin.settings.attributes.mass_update');

                    Route::put('mass-destroy', 'AttributeController@massDestroy')->name('admin.settings.attributes.mass_delete');

                    Route::get('download', 'AttributeController@download')->name('admin.settings.attributes.download');
                });


                // Lead Pipelines Routes
                Route::prefix('pipelines')->group(function () {
                    Route::get('', 'PipelineController@index')->name('admin.settings.pipelines.index');

                    Route::get('create', 'PipelineController@create')->name('admin.settings.pipelines.create');

                    Route::post('create', 'PipelineController@store')->name('admin.settings.pipelines.store');

                    Route::get('edit/{id?}', 'PipelineController@edit')->name('admin.settings.pipelines.edit');

                    Route::put('edit/{id}', 'PipelineController@update')->name('admin.settings.pipelines.update');

                    Route::delete('{id}', 'PipelineController@destroy')->name('admin.settings.pipelines.delete');
                });


                // Lead Sources Routes
                Route::prefix('sources')->group(function () {
                    Route::get('', 'SourceController@index')->name('admin.settings.sources.index');

                    Route::post('create', 'SourceController@store')->name('admin.settings.sources.store');

                    Route::get('edit/{id?}', 'SourceController@edit')->name('admin.settings.sources.edit');

                    Route::put('edit/{id}', 'SourceController@update')->name('admin.settings.sources.update');

                    Route::delete('{id}', 'SourceController@destroy')->name('admin.settings.sources.delete');
                });


                // Lead Types Routes
                Route::prefix('types')->group(function () {
                    Route::get('', 'TypeController@index')->name('admin.settings.types.index');

                    Route::post('create', 'TypeController@store')->name('admin.settings.types.store');

                    Route::get('edit/{id?}', 'TypeController@edit')->name('admin.settings.types.edit');

                    Route::put('edit/{id}', 'TypeController@update')->name('admin.settings.types.update');

                    Route::delete('{id}', 'TypeController@destroy')->name('admin.settings.types.delete');
                });


                // Email Templates Routes
                Route::prefix('email-templates')->group(function () {
                    Route::get('', 'EmailTemplateController@index')->name('admin.settings.email_templates.index');

                    Route::get('create', 'EmailTemplateController@create')->name('admin.settings.email_templates.create');

                    Route::post('create', 'EmailTemplateController@store')->name('admin.settings.email_templates.store');

                    Route::get('edit/{id?}', 'EmailTemplateController@edit')->name('admin.settings.email_templates.edit');

                    Route::put('edit/{id}', 'EmailTemplateController@update')->name('admin.settings.email_templates.update');

                    Route::delete('{id}', 'EmailTemplateController@destroy')->name('admin.settings.email_templates.delete');
                });


                // Workflows Routes
                Route::prefix('workflows')->group(function () {
                    Route::get('', 'WorkflowController@index')->name('admin.settings.workflows.index');

                    Route::get('create', 'WorkflowController@create')->name('admin.settings.workflows.create');

                    Route::post('create', 'WorkflowController@store')->name('admin.settings.workflows.store');

                    Route::get('edit/{id?}', 'WorkflowController@edit')->name('admin.settings.workflows.edit');

                    Route::put('edit/{id}', 'WorkflowController@update')->name('admin.settings.workflows.update');

                    Route::delete('{id}', 'WorkflowController@destroy')->name('admin.settings.workflows.delete');
                });


                // Tags Routes
                Route::prefix('tags')->group(function () {
                    Route::get('', 'TagController@index')->name('admin.settings.tags.index');

                    Route::post('create', 'TagController@store')->name('admin.settings.tags.store');

                    Route::get('edit/{id?}', 'TagController@edit')->name('admin.settings.tags.edit');

                    Route::put('edit/{id}', 'TagController@update')->name('admin.settings.tags.update');

                    Route::get('search', 'TagController@search')->name('admin.settings.tags.search');

                    Route::delete('{id}', 'TagController@destroy')->name('admin.settings.tags.delete');

                    Route::put('mass-destroy', 'TagController@massDestroy')->name('admin.settings.tags.mass_delete');
                });
            });

            // Configuration Routes
            Route::group([
                'prefix'    => 'configuration',
                'namespace' => 'Webkul\Admin\Http\Controllers\Configuration'
            ], function () {
                Route::get('{slug?}', 'ConfigurationController@index')->name('admin.configuration.index');

                Route::post('{slug?}', 'ConfigurationController@store')->name('admin.configuration.index.store');
            });
        });
    });
});
