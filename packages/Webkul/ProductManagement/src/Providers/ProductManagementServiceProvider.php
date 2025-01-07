<?php

namespace Webkul\ProductManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class ProductManagementServiceProvider extends ServiceProvider
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

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'product');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'productmanagement');

        Event::listen('admin.layout.head.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('productmanagement::components.layouts.style');
        });
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
            dirname(__DIR__) . '/Config/attribute_entity_types.php', 'attribute_entity_types'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/attribute_lookups.php', 'attribute_lookups'
        );

    }
}
