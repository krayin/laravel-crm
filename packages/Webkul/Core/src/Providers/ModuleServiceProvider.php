<?php

namespace Webkul\Core\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Core\Models\CoreConfig::class,
        \Webkul\Core\Models\Country::class,
        \Webkul\Core\Models\CountryState::class,
    ];
}