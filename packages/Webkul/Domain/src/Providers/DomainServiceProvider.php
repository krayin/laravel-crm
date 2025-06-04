<?php

namespace Webkul\Domain\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Webkul\Domain\Contracts\Domain::class,
            \Webkul\Domain\Models\Domain::class
        );

        $this->app->singleton(
            \Webkul\Tenant\Repositories\TenantRepository::class,
            function ($app) {
                return new \Webkul\Tenant\Repositories\TenantRepository($app);
            }
        );
    }
}
