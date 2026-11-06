<?php

namespace Webkul\Automation\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Automation\Helpers\Entity as EntityHelper;
use Webkul\Automation\Listeners\Entity;

class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        Event::listen('*', function ($eventName, array $data) {
            if (! in_array($eventName, data_get(config('workflows.trigger_entities'), '*.events.*.event'))) {
                return;
            }

            app(Entity::class)->process($eventName, current($data));
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

        /**
         * Scoped so one request or job builds the placeholder list once and Octane/queue workers
         * start each one fresh; the alias matches the leading-backslash string the Blade views resolve.
         */
        $this->app->scoped(EntityHelper::class);

        $this->app->alias(EntityHelper::class, '\Webkul\Automation\Helpers\Entity');
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/workflows.php', 'workflows');
    }
}
