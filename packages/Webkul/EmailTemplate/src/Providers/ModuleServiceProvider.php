<?php

namespace Webkul\EmailTemplate\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\EmailTemplate\Models\EmailTemplate;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        EmailTemplate::class,
    ];
}
