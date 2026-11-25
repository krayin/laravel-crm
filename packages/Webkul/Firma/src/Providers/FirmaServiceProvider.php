<?php

namespace Webkul\Firma\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Firma\Services\FirmaSettings;

class FirmaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FirmaSettings::class);

        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.admin');

        $this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php', 'acl');
    }
}
