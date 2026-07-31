<?php

namespace Webkul\WebForm\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\WebForm\Models\WebForm;
use Webkul\WebForm\Models\WebFormAttribute;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        WebForm::class,
        WebFormAttribute::class,
    ];
}
