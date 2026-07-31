<?php

namespace Webkul\Contact\Providers;

use Webkul\Contact\Models\Organization;
use Webkul\Contact\Models\Person;
use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Person::class,
        Organization::class,
    ];
}
