<?php

namespace Webkul\Email\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Email\Models\Email::class,
        \Webkul\Email\Models\Attachment::class,
    ];
}
