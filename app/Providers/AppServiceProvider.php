<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // URL::forceScheme('https');
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        if (config('app.env') === 'local') {
            URL::forceScheme('http');
        }
    }
}
