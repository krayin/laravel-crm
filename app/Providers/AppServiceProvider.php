<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(\App\Support\CssValidator::class);
        $this->app->singleton(\App\Support\BrandKitResolver::class);
        $this->app->singleton(\App\Support\ThemeSelectionResolver::class);
        $this->app->singleton(\App\Support\ThemeContextFactory::class);
        $this->app->singleton(\App\Repositories\BrandKitRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
