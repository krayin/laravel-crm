<?php

namespace Webkul\Attribute\Providers;

use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Attribute::class,
        AttributeOption::class,
        AttributeValue::class,
    ];
}
