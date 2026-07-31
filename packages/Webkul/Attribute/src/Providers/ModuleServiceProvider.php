<?php

namespace Webkul\Attribute\Providers;

use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var array{
     *  0: class-string<Attribute>,
     *  1: class-string<AttributeOption>,
     *  2: class-string<AttributeValue>
     * }
     */
    protected $models = [
        Attribute::class,
        AttributeOption::class,
        AttributeValue::class,
    ];
}
