<?php

namespace Webkul\Transaction\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class TransactionServiceProvider extends ServiceProvider
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



        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'transaction');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'transaction');

        Event::listen('admin.layout.head.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('transaction::components.layouts.style');
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
    }
}
