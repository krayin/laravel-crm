<?php

namespace Webkul\Tag\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Tag\Models\Tag;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Tag::class,
    ];
}
