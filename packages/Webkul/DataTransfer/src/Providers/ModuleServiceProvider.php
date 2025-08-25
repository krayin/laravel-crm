<?php

namespace Webkul\DataTransfer\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define models to map with repository interfaces.
     *
     * @var array
     */
    protected $models = [
        \Webkul\DataTransfer\Models\Import::class,
        \Webkul\DataTransfer\Models\ImportBatch::class,
    ];
}
