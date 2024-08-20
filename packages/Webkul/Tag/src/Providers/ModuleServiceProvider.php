<?php

namespace Webkul\Tag\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Tag\Models\Tag::class,
    ];
}
