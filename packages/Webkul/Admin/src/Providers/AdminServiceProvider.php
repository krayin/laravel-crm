<?php

namespace Webkul\Admin\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Admin\Http\Middleware\Locale;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        include __DIR__.'/../Http/helpers.php';

        Route::middleware('web')->group(__DIR__.'/../Routes/web.php');

        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'admin');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'admin');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'admin');

        $this->app->bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, \Webkul\Admin\Exceptions\Handler::class);

        $router->aliasMiddleware('user', \Webkul\Admin\Http\Middleware\Bouncer::class);

        $router->aliasMiddleware('admin_locale', Locale::class);

        Relation::morphMap([
            'leads'         => 'Webkul\Lead\Models\Lead',
            'products'      => 'Webkul\Product\Models\Product',
            'persons'       => 'Webkul\Contact\Models\Person',
            'organizations' => 'Webkul\Contact\Models\Organization',
            'quotes'        => 'Webkul\Quote\Models\Quote',
            'warehouses'    => 'Webkul\Warehouse\Models\Warehouse',
        ]);

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();

        $this->registerConfig();
    }

    /**
     * Register Bouncer as a singleton.
     */
    protected function registerFacades(): void
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('Bouncer', \Webkul\Admin\Facades\Bouncer::class);

        $this->app->singleton('bouncer', function () {
            return new \Webkul\Admin\Bouncer();
        });
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php', 'acl');

        $this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.admin');

        $this->mergeConfigFrom(dirname(__DIR__).'/Config/core_config.php', 'core_config');

        $this->mergeConfigFrom(dirname(__DIR__).'/Config/attribute_lookups.php', 'attribute_lookups');

        $this->mergeConfigFrom(dirname(__DIR__).'/Config/attribute_entity_types.php', 'attribute_entity_types');
    }
}
