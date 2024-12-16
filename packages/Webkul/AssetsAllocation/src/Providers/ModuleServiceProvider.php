<?php

namespace Webkul\AssetsAllocation\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * The models to be used by this module.
     *
     * @var array
     */
    protected $models = [
        \Webkul\AssetsAllocation\Models\AssetsAllocation::class,
    ];
}
