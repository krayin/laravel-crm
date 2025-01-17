<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
     */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
     */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
     */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
     */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Admin URL
    |--------------------------------------------------------------------------
    |
    | This URL suffix is used to define the admin url for example
    | admin/ or backend/
    |
     */

    'admin_path' => env('APP_ADMIN_PATH', 'admin'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
     */

    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
     */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Available Locales Configuration
    |--------------------------------------------------------------------------
    |
    | The application available locale determines the supported locales
    | by application
    |
     */

    'available_locales' => [
        'ar'    => 'Arabic',
        'en'    => 'English',
        'es'    => 'Español',
        'fa'    => 'Persian',
        'pt_BR' => 'Portuguese',
        'tr'    => 'Türkçe',
        'vi'    => 'Vietnamese',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
     */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
     */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Base Currency Code
    |--------------------------------------------------------------------------
    |
    | Here you may specify the base currency code for your application.
    |
     */

    'currency' => env('APP_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
     */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
     */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        Barryvdh\DomPDF\ServiceProvider::class,
        Konekt\Concord\ConcordServiceProvider::class,
        Prettus\Repository\Providers\RepositoryServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /*
         * Webkul Service Providers...
         */
        Webkul\Activity\Providers\ActivityServiceProvider::class,
        Webkul\Admin\Providers\AdminServiceProvider::class,
        Webkul\Attribute\Providers\AttributeServiceProvider::class,
        Webkul\Automation\Providers\WorkflowServiceProvider::class,
        Webkul\Contact\Providers\ContactServiceProvider::class,
        Webkul\Core\Providers\CoreServiceProvider::class,
        Webkul\DataGrid\Providers\DataGridServiceProvider::class,
        Webkul\DataTransfer\Providers\DataTransferServiceProvider::class,
        Webkul\EmailTemplate\Providers\EmailTemplateServiceProvider::class,
        Webkul\Email\Providers\EmailServiceProvider::class,
        Webkul\Marketing\Providers\MarketingServiceProvider::class,
        Webkul\Installer\Providers\InstallerServiceProvider::class,
        Webkul\Lead\Providers\LeadServiceProvider::class,
        Webkul\Product\Providers\ProductServiceProvider::class,
        Webkul\Quote\Providers\QuoteServiceProvider::class,
        Webkul\Tag\Providers\TagServiceProvider::class,
        Webkul\User\Providers\UserServiceProvider::class,
        Webkul\Warehouse\Providers\WarehouseServiceProvider::class,
        Webkul\WebForm\Providers\WebFormServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
     */

    'aliases' => Facade::defaultAliases()->merge([])->toArray(),

];
