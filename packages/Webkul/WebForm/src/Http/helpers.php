<?php

use Webkul\WebForm\Vite as WebFormVite;

if (! function_exists('webform_vite')) {
    function webform_vite(): WebFormVite
    {
        return app(WebFormVite::class);
    }
}
