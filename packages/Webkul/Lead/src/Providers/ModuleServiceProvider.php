<?php

namespace Webkul\Lead\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Lead\Models\Lead::class,
    ];
}