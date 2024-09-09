<?php

namespace Webkul\DataTransfer\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\DataTransfer\Models\Import::class,
        \Webkul\DataTransfer\Models\ImportBatch::class,
    ];
}
