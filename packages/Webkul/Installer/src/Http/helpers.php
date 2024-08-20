<?php

use Webkul\Installer\Vite as InstallerVite;

if (! function_exists('installer_vite')) {
    function installer_vite(): InstallerVite
    {
        return app(InstallerVite::class);
    }
}
