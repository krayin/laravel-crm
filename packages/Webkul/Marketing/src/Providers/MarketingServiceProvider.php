<?php

namespace Webkul\Marketing\Providers;

use Illuminate\Support\ServiceProvider;

class MarketingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(ModuleServiceProvider::class);
    }
}
