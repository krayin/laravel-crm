<?php

namespace Webkul\Email\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Email\Models\Attachment;
use Webkul\Email\Models\Email;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Email::class,
        Attachment::class,
    ];
}
