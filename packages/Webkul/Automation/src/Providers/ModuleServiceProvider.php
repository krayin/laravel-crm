<?php

namespace Webkul\Automation\Providers;

use Webkul\Automation\Models\Webhook;
use Webkul\Automation\Models\Workflow;
use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define the modals to map with this module.
     *
     * @var array
     */
    protected $models = [
        Workflow::class,
        Webhook::class,
    ];
}
