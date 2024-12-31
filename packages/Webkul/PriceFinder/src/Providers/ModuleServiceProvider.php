<?php

namespace Webkul\PriceFinder\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * The models to be used by this module.
     *
     * @var array
     */
    protected $models = [
        \Webkul\PriceFinder\Models\PriceFinder::class,
    ];
}
