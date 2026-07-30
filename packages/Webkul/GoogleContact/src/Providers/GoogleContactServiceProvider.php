<?php

namespace Webkul\GoogleContact\Providers;

use Illuminate\Support\ServiceProvider;

class GoogleContactServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'google_contact');

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
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
