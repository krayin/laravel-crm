<?php

use Webkul\WebForm\Vite as WebFormVite;

function webform_vite(): WebFormVite
{
    return app(WebFormVite::class);
}
