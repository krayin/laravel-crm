<?php

namespace Webkul\Admin\Providers;

use Webkul\Admin\Models\Notification;
use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Notification::class,
    ];
}
