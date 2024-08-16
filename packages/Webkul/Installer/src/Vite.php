<?php

namespace Webkul\Installer;

use Illuminate\Support\Facades\Vite as BaseVite;

class Vite
{
    protected $hotFile = 'installer-vite.hot';

    protected $buildDirectory = 'installer/build';

    protected $packageAssetDirectory = 'src/Resources/assets';

    /**
     * Return the asset URL.
     *
     * @return string
     */
    public function asset(string $filename)
    {
        $url = trim($filename, '/');

        $viteUrl = trim($this->packageAssetDirectory, '/').'/'.$url;

        return BaseVite::useHotFile($this->hotFile)
            ->useBuildDirectory($this->buildDirectory)
            ->asset($viteUrl);
    }

    /**
     * Set krayin vite.
     *
     * @param  mixed  $entryPoints
     * @return mixed
     */
    public function set($entryPoints)
    {
        return BaseVite::useHotFile($this->hotFile)
            ->useBuildDirectory($this->buildDirectory)
            ->withEntryPoints($entryPoints);
    }
}
