<?php

namespace Webkul\Marketing\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Marketing\Models\Campaign;
use Webkul\Marketing\Models\Event;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define the module's array.
     *
     * @var array
     */
    protected $models = [
        Event::class,
        Campaign::class,
    ];
}
