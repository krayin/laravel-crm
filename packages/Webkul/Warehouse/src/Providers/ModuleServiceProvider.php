<?php

namespace Webkul\Warehouse\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Warehouse\Models\Location;
use Webkul\Warehouse\Models\Warehouse;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Location::class,
        Warehouse::class,
    ];
}
