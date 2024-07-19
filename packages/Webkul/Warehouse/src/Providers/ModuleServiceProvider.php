<?php

namespace Webkul\Warehouse\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Warehouse\Models\Location::class,
        \Webkul\Warehouse\Models\Warehouse::class,
    ];
}
