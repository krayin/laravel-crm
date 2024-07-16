<?php

namespace Webkul\DataGrid\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\DataGrid\Models\SavedFilter::class,
    ];
}
