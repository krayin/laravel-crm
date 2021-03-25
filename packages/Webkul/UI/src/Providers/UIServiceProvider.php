<?php

namespace Webkul\UI\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'ui');

        Paginator::defaultView('ui::partials.pagination');
        
        Paginator::defaultSimpleView('ui::partials.pagination');
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
