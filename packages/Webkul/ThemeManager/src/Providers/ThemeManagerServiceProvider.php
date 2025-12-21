<?php

namespace Webkul\ThemeManager\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Webkul\ThemeManager\Helpers\ThemeHelper;
use Webkul\ThemeManager\Http\Middleware\ThemeMiddleware;

class ThemeManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge system config
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'theme-manager'
        );

        // Merge menu config
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/menu.php',
            'menu.admin'
        );

        // Register singleton for theme helper
        $this->app->singleton('theme', function ($app) {
            return new ThemeHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(dirname(__DIR__).'/../Database/Migrations');

        // Load routes
        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/web.php');

        // Load views
        $this->loadViewsFrom(dirname(__DIR__).'/../Resources/views', 'theme-manager');

        // Load translations
        $this->loadTranslationsFrom(dirname(__DIR__).'/../Resources/lang', 'theme-manager');

        // Publish assets
        $this->publishes([
            dirname(__DIR__).'/../Resources/assets' => public_path('vendor/theme-manager'),
        ], 'theme-manager-assets');

        // Override login view with higher priority
        $this->overrideLoginView();

        // Share theme config with all views
        $this->shareThemeConfig();

        // Register middleware
        $this->registerMiddleware();
    }

    /**
     * Share theme configuration with all views.
     *
     * @return void
     */
    protected function shareThemeConfig()
    {
        View::composer('*', function ($view) {
            $themeHelper = app('theme');
            $view->with('themeConfig', $themeHelper->getConfig());
        });
    }

    /**
     * Override the login view with theme-manager's custom view.
     *
     * @return void
     */
    protected function overrideLoginView()
    {
        // Register our views directory with 'admin' namespace
        // This will override Admin package views if they have the same path
        View::addNamespace('admin', dirname(__DIR__).'/../Resources/views/admin');
    }

    /**
     * Register the theme middleware.
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $router = $this->app['router'];

        $router->pushMiddlewareToGroup('web', ThemeMiddleware::class);
    }
}
