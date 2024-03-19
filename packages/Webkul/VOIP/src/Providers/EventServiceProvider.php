<?php

namespace Webkul\VOIP\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('admin.layout.head', function ($e) {
            $e->addTemplate('voip::layouts.style');
        });

        Event::listen('admin.layouts.nav-top.quick-create.before', function ($e) {
            $e->addTemplate('voip::layouts.nav-top.phone-icon');
        });
    }
}
