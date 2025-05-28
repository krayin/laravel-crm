<?php

namespace Webkul\Tenant\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        logger('Teste caiu aqui');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Webkul\Tenant\Contracts\Tenant::class,
            \Webkul\Tenant\Models\Tenant::class
        );

        $this->app->singleton(
            \Webkul\Tenant\Repositories\TenantRepository::class,
            function ($app) {
                return new \Webkul\Tenant\Repositories\TenantRepository(
                    $app->make(\Webkul\Tenant\Contracts\Tenant::class)
                );
            }
        );
    }
}
