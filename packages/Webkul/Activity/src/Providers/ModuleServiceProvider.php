<?php

namespace Webkul\Activity\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Activity\Models\Activity::class,
        \Webkul\Activity\Models\File::class,
        \Webkul\Activity\Models\Participant::class,
    ];
}
