<?php

namespace Webkul\Contact\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Contact\Models\Person::class,
        \Webkul\Contact\Models\Organization::class,
    ];
}