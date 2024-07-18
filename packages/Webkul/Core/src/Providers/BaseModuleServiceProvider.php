<?php

namespace Webkul\Core\Providers;

use Konekt\Concord\BaseModuleServiceProvider as ConcordBaseModuleServiceProvider;

class BaseModuleServiceProvider extends ConcordBaseModuleServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->areMigrationsEnabled()) {
            $this->registerMigrations();
        }

        if ($this->areModelsEnabled()) {
            $this->registerModels();
            $this->registerEnums();
            $this->registerRequestTypes();
        }

        if ($this->areViewsEnabled()) {
            $this->registerViews();
        }

        if ($routes = $this->config('routes', true)) {
            $this->registerRoutes($routes);
        }
    }
}
