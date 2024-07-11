<?php

if (! function_exists('bouncer')) {
    function bouncer()
    {
        return app()->make('bouncer');
    }
}

function vite()
{
    return app(\Webkul\Core\Vite::class);
}
