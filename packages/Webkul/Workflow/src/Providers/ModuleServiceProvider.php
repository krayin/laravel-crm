<?php

namespace Webkul\Workflow\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Workflow\Models\Workflow::class,
    ];
}