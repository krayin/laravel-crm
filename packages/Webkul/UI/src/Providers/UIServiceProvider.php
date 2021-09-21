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
        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/ui/assets'),
        ], 'public');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'ui');
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
