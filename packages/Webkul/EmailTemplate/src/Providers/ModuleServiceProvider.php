<?php

namespace Webkul\EmailTemplate\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\EmailTemplate\Models\EmailTemplate::class,
    ];
}
