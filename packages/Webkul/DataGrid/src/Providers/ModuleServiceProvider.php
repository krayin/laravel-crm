<?php

namespace Webkul\DataGrid\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\DataGrid\Models\SavedFilter;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        SavedFilter::class,
    ];
}
