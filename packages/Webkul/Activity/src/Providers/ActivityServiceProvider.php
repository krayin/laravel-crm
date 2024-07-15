<?php

namespace Webkul\Activity\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ActivityServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}