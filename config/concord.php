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
        \Webkul\Expense\Providers\ModuleServiceProvider::class,
        \Webkul\Client\Providers\ModuleServiceProvider::class,
        \Webkul\Approval\Providers\ModuleServiceProvider::class,
        \Webkul\Order\Providers\ModuleServiceProvider::class,
        \Webkul\Invoice\Providers\ModuleServiceProvider::class,
        \Webkul\Asset\Providers\ModuleServiceProvider::class,
        \Webkul\Employee\Providers\ModuleServiceProvider::class,
        \Webkul\AssetsAllocation\Providers\ModuleServiceProvider::class,
    ],

    'register_route_models' => true,
];
