<?php

namespace Webkul\Consignment\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class ConsignmentServiceProvider extends ServiceProvider
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

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'consignment');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'consignment');

        Event::listen('admin.layout.head.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('consignment::components.layouts.style');
        });

        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('consignment/build/assets'),
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

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/attribute_entity_types.php', 'attribute_entity_types'
        );
    }
}
