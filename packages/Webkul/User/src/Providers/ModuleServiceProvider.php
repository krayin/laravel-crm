<?php

namespace Webkul\User\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\User\Models\User::class,
        \Webkul\User\Models\Role::class,
    ];
}