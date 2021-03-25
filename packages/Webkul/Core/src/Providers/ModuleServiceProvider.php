<?php

namespace Webkul\Core\Providers;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    protected $models = [
        \Webkul\Core\Models\CoreConfig::class,
    ];
}