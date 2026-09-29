<?php

namespace Webkul\Marketing\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Webkul\Marketing\Console\Commands\CampaignCommand;

class MarketingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('campaign:process')->daily();
        });
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerCommands();

        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Register the commands.
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CampaignCommand::class,
            ]);
        }
    }
}
