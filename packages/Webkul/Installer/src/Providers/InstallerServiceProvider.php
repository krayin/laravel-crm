<?php

namespace Webkul\Installer\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Installer\Console\Commands\Installer as InstallerCommand;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'installer');

        Event::listen('bagisto.installed', 'Webkul\Installer\Listeners\Installer@installed');
    }

    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }
        
        $this->commands([InstallerCommand::class]);
    }
}
