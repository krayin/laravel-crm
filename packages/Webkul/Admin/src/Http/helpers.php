<?php

use Webkul\Admin\Vite as AdminVite;

if (! function_exists('bouncer')) {
    function bouncer()
    {
        return app()->make('bouncer');
    }
}

if (! function_exists('admin_vite')) {
    function admin_vite(): AdminVite
    {
        return app(AdminVite::class);
    }
}
