<?php

namespace Stancl\Tenancy;

use Illuminate\Foundation\Vite as BaseVite;

class Vite extends BaseVite // todo move to a different directory in v4
{
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    protected function assetPath($path, $secure = null)
    {
        // In Laravel 9, the property doesn't exist.
        return (isset($this->assetPathResolver) && $this->assetPathResolver)
            ? ($this->assetPathResolver)($path, $secure)
            : global_asset($path);
    }
}
