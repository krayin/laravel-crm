<?php

use Webkul\Admin\Providers\ModuleServiceProvider as AdminModuleServiceProvider;
use Webkul\Attribute\Providers\ModuleServiceProvider as AttributeModuleServiceProvider;
use Webkul\Automation\Providers\ModuleServiceProvider as AutomationModuleServiceProvider;
use Webkul\Contact\Providers\ModuleServiceProvider as ContactModuleServiceProvider;
use Webkul\Core\Providers\ModuleServiceProvider as CoreModuleServiceProvider;
use Webkul\DataGrid\Providers\ModuleServiceProvider as DataGridModuleServiceProvider;
use Webkul\DataTransfer\Providers\ModuleServiceProvider as DataTransferModuleServiceProvider;
use Webkul\Email\Providers\ModuleServiceProvider as EmailModuleServiceProvider;
use Webkul\EmailTemplate\Providers\ModuleServiceProvider as EmailTemplateModuleServiceProvider;
use Webkul\Lead\Providers\ModuleServiceProvider as LeadModuleServiceProvider;
use Webkul\Product\Providers\ModuleServiceProvider as ProductModuleServiceProvider;
use Webkul\Quote\Providers\ModuleServiceProvider as QuoteModuleServiceProvider;
use Webkul\Tag\Providers\ModuleServiceProvider as TagModuleServiceProvider;
use Webkul\User\Providers\ModuleServiceProvider as UserModuleServiceProvider;
use Webkul\Warehouse\Providers\ModuleServiceProvider as WarehouseModuleServiceProvider;
use Webkul\WebForm\Providers\ModuleServiceProvider as WebFormModuleServiceProvider;

return [
    'modules' => [
        DataTransferModuleServiceProvider::class,
        AdminModuleServiceProvider::class,
        AttributeModuleServiceProvider::class,
        AutomationModuleServiceProvider::class,
        ContactModuleServiceProvider::class,
        CoreModuleServiceProvider::class,
        DataGridModuleServiceProvider::class,
        EmailTemplateModuleServiceProvider::class,
        EmailModuleServiceProvider::class,
        LeadModuleServiceProvider::class,
        ProductModuleServiceProvider::class,
        QuoteModuleServiceProvider::class,
        TagModuleServiceProvider::class,
        UserModuleServiceProvider::class,
        WarehouseModuleServiceProvider::class,
        WebFormModuleServiceProvider::class,
        DataTransferModuleServiceProvider::class,
    ],

    'register_route_models' => true,
];
