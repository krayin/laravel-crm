<?php

namespace Webkul\VOIP\Providers;

use Illuminate\Support\ServiceProvider;

class VOIPServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'voip');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'voip');

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
