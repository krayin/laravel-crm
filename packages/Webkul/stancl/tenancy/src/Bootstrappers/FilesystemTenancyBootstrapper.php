<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Bootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class FilesystemTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var Application */
    protected $app;

    /** @var array */
    public $originalPaths = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->originalPaths = [
            'disks' => [],
            'storage' => $this->app->storagePath(),
            'asset_url' => $this->app['config']['app.asset_url'],
        ];

        $this->app['url']->macro('setAssetRoot', function ($root) {
            $this->assetRoot = $root;

            return $this;
        });
    }

    public function bootstrap(Tenant $tenant)
    {
        $suffix = $this->app['config']['tenancy.filesystem.suffix_base'] . $tenant->getTenantKey();

        // storage_path()
        if ($this->app['config']['tenancy.filesystem.suffix_storage_path'] ?? true) {
            $this->app->useStoragePath($this->originalPaths['storage'] . "/{$suffix}");
        }

        // asset()
        if ($this->app['config']['tenancy.filesystem.asset_helper_tenancy'] ?? true) {
            if ($this->originalPaths['asset_url']) {
                $this->app['config']['app.asset_url'] = ($this->originalPaths['asset_url'] ?? $this->app['config']['app.url']) . "/$suffix";
                $this->app['url']->setAssetRoot($this->app['config']['app.asset_url']);
            } else {
                $this->app['url']->setAssetRoot($this->app['url']->route('stancl.tenancy.asset', ['path' => '']));
            }
        }

        // Storage facade
        Storage::forgetDisk($this->app['config']['tenancy.filesystem.disks']);

        foreach ($this->app['config']['tenancy.filesystem.disks'] as $disk) {
            $originalRoot = $this->app['config']["filesystems.disks.{$disk}.root"];
            $this->originalPaths['disks'][$disk] = $originalRoot;

            $finalPrefix = str_replace(
                ['%storage_path%', '%tenant%'],
                [storage_path(), $tenant->getTenantKey()],
                $this->app['config']["tenancy.filesystem.root_override.{$disk}"] ?? '',
            );

            if (! $finalPrefix) {
                $finalPrefix = $originalRoot
                    ? rtrim($originalRoot, '/') . '/'. $suffix
                    : $suffix;
            }

            $this->app['config']["filesystems.disks.{$disk}.root"] = $finalPrefix;
        }
    }

    public function revert()
    {
        // storage_path()
        $this->app->useStoragePath($this->originalPaths['storage']);

        // asset()
        $this->app['config']['app.asset_url'] = $this->originalPaths['asset_url'];
        $this->app['url']->setAssetRoot($this->app['config']['app.asset_url']);

        // Storage facade
        Storage::forgetDisk($this->app['config']['tenancy.filesystem.disks']);
        foreach ($this->app['config']['tenancy.filesystem.disks'] as $disk) {
            $this->app['config']["filesystems.disks.{$disk}.root"] = $this->originalPaths['disks'][$disk];
        }
    }
}
