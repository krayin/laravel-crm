<?php

return [
    'modules' => [
        \Webkul\Activity\Providers\ModuleServiceProvider::class,
        \Webkul\Admin\Providers\ModuleServiceProvider::class,
        \Webkul\Attribute\Providers\ModuleServiceProvider::class,
        \Webkul\Automation\Providers\ModuleServiceProvider::class,
        \Webkul\Contact\Providers\ModuleServiceProvider::class,
        \Webkul\Core\Providers\ModuleServiceProvider::class,
        \Webkul\DataGrid\Providers\ModuleServiceProvider::class,
        \Webkul\EmailTemplate\Providers\ModuleServiceProvider::class,
        \Webkul\Email\Providers\ModuleServiceProvider::class,
        \Webkul\Lead\Providers\ModuleServiceProvider::class,
        \Webkul\Product\Providers\ModuleServiceProvider::class,
        \Webkul\Quote\Providers\ModuleServiceProvider::class,
        \Webkul\Tag\Providers\ModuleServiceProvider::class,
        \Webkul\User\Providers\ModuleServiceProvider::class,
        \Webkul\Warehouse\Providers\ModuleServiceProvider::class,
        \Webkul\WebForm\Providers\ModuleServiceProvider::class,

        // created by shubham
        \Webkul\Announcement\Providers\ModuleServiceProvider::class,
        \Webkul\Consignment\Providers\ModuleServiceProvider::class,
        \Webkul\Inventory\Providers\ModuleServiceProvider::class,
        \Webkul\PriceFinder\Providers\ModuleServiceProvider::class,
        \Webkul\Reporting\Providers\ModuleServiceProvider::class,
        \Webkul\Transaction\Providers\ModuleServiceProvider::class,
        \Webkul\Budget\Providers\ModuleServiceProvider::class,
        \Webkul\AssetManagement\Providers\ModuleServiceProvider::class,
        \Webkul\OrderManagement\Providers\ModuleServiceProvider::class,
        \Webkul\SecondarySales\Providers\ModuleServiceProvider::class,
        \Webkul\RepositoryDetails\Providers\ModuleServiceProvider::class,
        \Webkul\ProductManagement\Providers\ModuleServiceProvider::class,
    ],

    'register_route_models' => true,
];
