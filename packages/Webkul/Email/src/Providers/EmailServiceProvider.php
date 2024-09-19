<?php

namespace Webkul\Email\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->app->bind(
            \Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor::class,
            \Webkul\Email\InboundEmailProcessor\SendgridEmailProcessor::class
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {}
}
