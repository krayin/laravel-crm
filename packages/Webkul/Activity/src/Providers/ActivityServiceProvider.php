<?php

namespace Webkul\Activity\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Webkul\Activity\Contracts\Activity as ActivityContract;
use Webkul\Activity\Contracts\File as FileContract;
use Webkul\Activity\Contracts\Participant as ParticipantContract;
use Webkul\Activity\Models\Activity;
use Webkul\Activity\Models\File;
use Webkul\Activity\Models\Participant;

class ActivityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * Adds Laravel container bindings as a fallback so repositories that
     * type-hint the contract interfaces resolve even if Concord's
     * ModuleServiceProvider::boot() has not yet run.
     */
    public function register(): void
    {
        $this->app->bindIf(ActivityContract::class, Activity::class);
        $this->app->bindIf(FileContract::class, File::class);
        $this->app->bindIf(ParticipantContract::class, Participant::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Guarantee that Concord's model-proxy registry contains the Activity
        // bindings after all providers have booted.  ModelProxy::modelClass()
        // looks up $concord->model($contract) — a separate store from the
        // Laravel container — so this registration is strictly necessary for
        // proxies like ActivityProxy used in Eloquent relationships.
        $this->app->booted(function () {
            $concord = app('concord');

            if (! $concord->model(ActivityContract::class)) {
                $concord->registerModel(ActivityContract::class, Activity::class);
            }

            if (! $concord->model(FileContract::class)) {
                $concord->registerModel(FileContract::class, File::class);
            }

            if (! $concord->model(ParticipantContract::class)) {
                $concord->registerModel(ParticipantContract::class, Participant::class);
            }
        });
    }
}
