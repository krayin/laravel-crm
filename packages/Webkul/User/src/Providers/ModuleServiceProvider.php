<?php

namespace Webkul\User\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\User\Models\Group::class,
        \Webkul\User\Models\Role::class,
        \Webkul\User\Models\User::class,
    ];
}
