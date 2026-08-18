<?php

namespace Webkul\User\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\User\Models\Group;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Group::class,
        Role::class,
        User::class,
    ];
}
