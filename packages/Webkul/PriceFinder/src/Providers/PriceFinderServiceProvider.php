<?php

namespace Webkul\PriceFinder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class PriceFinderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/breadcrumbs.php');


        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'pricefinder');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'pricefinder');

        Event::listen('admin.layout.head.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('pricefinder::components.layouts.style');
        });

        $this->publishes([
            __DIR__ . '/../Resources/Assets' => public_path('priceFinder/build/assets'),
        ], 'public');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/krayin-vite.php', 'krayin-vite.viters'
        );
    }
}
