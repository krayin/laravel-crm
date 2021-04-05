<?php

namespace Webkul\Attribute\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Attribute\Models\Attribute::class,
        \Webkul\Attribute\Models\AttributeOption::class,
    ];
}