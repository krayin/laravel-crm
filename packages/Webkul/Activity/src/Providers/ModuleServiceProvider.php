<?php

namespace Webkul\Activity\Providers;

use Webkul\Activity\Models\Activity;
use Webkul\Activity\Models\File;
use Webkul\Activity\Models\Participant;
use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Activity::class,
        File::class,
        Participant::class,
    ];
}
