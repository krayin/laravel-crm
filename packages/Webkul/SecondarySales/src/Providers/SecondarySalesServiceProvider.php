<?php

namespace Webkul\SecondarySales\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class SecondarySalesServiceProvider extends ServiceProvider
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

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'secondarysales');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'secondarysales');

        Event::listen('admin.layout.head.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('secondarysales::components.layouts.style');
        });

        // publishing the view files for the assets
        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('secondarySales/build/assets'),
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
