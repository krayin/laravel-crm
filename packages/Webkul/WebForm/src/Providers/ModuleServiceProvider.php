<?php

namespace Webkul\WebForm\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\WebForm\Models\WebForm::class,
        \Webkul\WebForm\Models\WebFormAttribute::class,
    ];
}
