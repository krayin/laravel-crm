<?php

namespace Webkul\Email\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Email\Models\Attachment::class,
        \Webkul\Email\Models\Email::class,
        \Webkul\Email\Models\Thread::class,
    ];
}