<?php

namespace Webkul\Quote\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Quote\Models\Quote::class,
        \Webkul\Quote\Models\QuoteItem::class,
    ];
}
