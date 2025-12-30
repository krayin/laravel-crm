<?php

namespace SuiteZap\LawFirm\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Models to be registered.
     *
     * @var array
     */
    protected $models = [
        // Models will be registered here
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/module.php',
            'lawfirm'
        );
    }
}
