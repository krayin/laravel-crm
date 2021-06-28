<?php

namespace Webkul\Lead\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Lead\Models\Activity::class,
        \Webkul\Lead\Models\File::class,
        \Webkul\Lead\Models\Lead::class,
        \Webkul\Lead\Models\LeadProducts::class,
        \Webkul\Lead\Models\Pipeline::class,
        \Webkul\Lead\Models\PipelineStage::class,
        \Webkul\Lead\Models\Product::class,
        \Webkul\Lead\Models\Source::class,
        \Webkul\Lead\Models\Stage::class,
        \Webkul\Lead\Models\Type::class,
    ];
}