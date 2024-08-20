<?php

namespace Webkul\Contact\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Contact\Models\Person::class,
        \Webkul\Contact\Models\Organization::class,
    ];
}
