<?php

return [
    'modules' => [
        \Webkul\Activity\Providers\ModuleServiceProvider::class,
        \Webkul\Admin\Providers\ModuleServiceProvider::class,
        \Webkul\Attribute\Providers\ModuleServiceProvider::class,
        \Webkul\Contact\Providers\ModuleServiceProvider::class,
        \Webkul\Core\Providers\ModuleServiceProvider::class,
        \Webkul\Email\Providers\ModuleServiceProvider::class,
        \Webkul\EmailTemplate\Providers\ModuleServiceProvider::class,
        \Webkul\Lead\Providers\ModuleServiceProvider::class,
        \Webkul\Product\Providers\ModuleServiceProvider::class,
        \Webkul\Quote\Providers\ModuleServiceProvider::class,
        \Webkul\Tag\Providers\ModuleServiceProvider::class,
        \Webkul\UI\Providers\ModuleServiceProvider::class,
        \Webkul\User\Providers\ModuleServiceProvider::class,
        \Webkul\Workflow\Providers\ModuleServiceProvider::class,
    ],
    'register_route_models' => true
];
