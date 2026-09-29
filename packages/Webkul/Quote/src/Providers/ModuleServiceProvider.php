<?php

namespace Webkul\Quote\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\Quote\Models\Quote;
use Webkul\Quote\Models\QuoteItem;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Quote::class,
        QuoteItem::class,
    ];
}
