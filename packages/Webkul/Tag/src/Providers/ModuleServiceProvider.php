<?php

namespace Webkul\Tag\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Tag\Models\Tag::class,
    ];
}