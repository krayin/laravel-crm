<?php

namespace Webkul\Product\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductInventory;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Product::class,
        ProductInventory::class,
    ];
}
