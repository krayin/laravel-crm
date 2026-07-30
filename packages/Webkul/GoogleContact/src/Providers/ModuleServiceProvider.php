<?php

namespace Webkul\GoogleContact\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\GoogleContact\Models\ContactExportBatch;
use Webkul\GoogleContact\Models\ContactExportBatchItem;
use Webkul\GoogleContact\Models\GoogleContactAccount;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        GoogleContactAccount::class,
        ContactExportBatch::class,
        ContactExportBatchItem::class,
    ];
}
