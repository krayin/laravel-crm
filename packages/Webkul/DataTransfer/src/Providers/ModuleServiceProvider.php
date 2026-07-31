<?php

namespace Webkul\DataTransfer\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\DataTransfer\Models\Import;
use Webkul\DataTransfer\Models\ImportBatch;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define models to map with repository interfaces.
     *
     * @var array
     */
    protected $models = [
        Import::class,
        ImportBatch::class,
    ];
}
