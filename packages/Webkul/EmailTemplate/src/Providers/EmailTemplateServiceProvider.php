<?php

namespace Webkul\EmailTemplate\Providers;

use Illuminate\Support\ServiceProvider;

class EmailTemplateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
