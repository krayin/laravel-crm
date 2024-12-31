<?php

namespace Webkul\Announcement\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * The models to be used by this module.
     *
     * @var array
     */
    protected $models = [
        \Webkul\Announcement\Models\Announcement::class,
    ];
}
