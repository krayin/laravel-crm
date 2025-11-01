<?php

namespace Webkul\FeatureFlag\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        // Add models here if needed in the future
    ];

    protected $requests = [
        // Add form requests here if needed
    ];

    public function boot()
    {
        parent::boot();

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'featureflag');
    }

    public function register()
    {
        parent::register();
        
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}