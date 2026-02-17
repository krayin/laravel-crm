<?php

namespace Webkul\ThemeManager\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        \Webkul\ThemeManager\Models\ThemeConfig::class,
    ];
}
