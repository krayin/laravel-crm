<?php

namespace Famindo\AnalyticalCRM\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Routes (admin)
        Route::middleware(['web', 'admin_locale', 'user'])
            ->prefix(config('app.admin_path'))
            ->group(__DIR__.'/../../routes/admin.php');

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Views
        $this->loadViewsFrom(__DIR__.'/../../Resources/views', 'analyticalcrm');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge ACL and Admin Menu configuration
        $this->mergeConfigFrom(__DIR__.'/../Config/acl.php', 'acl');
        $this->mergeConfigFrom(__DIR__.'/../Config/menu.php', 'menu.admin');

        // Register console commands (also for HTTP to allow Artisan::call)
        $this->commands([
            \Famindo\AnalyticalCRM\Console\AnalyticsApriori::class,
        ]);
    }
}
