<?php

namespace Webkul\Workflow\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Event::listen('*', function ($eventName, array $data) {
            if (! in_array($eventName, data_get(config('workflows.trigger_entities'), '*.events.*.event'))) {
                return;
            }

            app(\Webkul\Workflow\Listeners\Entity::class)->process($eventName, current($data));
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/workflows.php', 'workflows');
    }
}