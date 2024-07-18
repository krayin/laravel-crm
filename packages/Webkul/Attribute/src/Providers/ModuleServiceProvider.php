<?php

namespace Webkul\Attribute\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Attribute\Models\Attribute::class,
        \Webkul\Attribute\Models\AttributeOption::class,
        \Webkul\Attribute\Models\AttributeValue::class,
    ];
}
