<?php

use Webkul\Admin\Vite as AdminVite;

if (! function_exists('bouncer')) {
    function bouncer()
    {
        return app()->make('bouncer');
    }
}

function admin_vite(): AdminVite
{
    return app(AdminVite::class);
}
