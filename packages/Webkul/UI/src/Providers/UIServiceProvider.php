<?php

namespace Webkul\UI\Providers;

use Illuminate\Support\ServiceProvider;

class UIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'ui');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'ui');

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/ui/assets'),
        ], 'public');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }
}
