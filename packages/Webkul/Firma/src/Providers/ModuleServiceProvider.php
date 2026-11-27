<?php

namespace Webkul\Firma\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Models\WebhookEvent;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        SignatureRequest::class,
        WebhookEvent::class,
    ];
}
