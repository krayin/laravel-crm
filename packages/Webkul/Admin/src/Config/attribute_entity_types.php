<?php

return [
    'leads'         => [
        'name'       => 'Lead',
        'repository' => 'Webkul\Lead\Repositories\LeadRepository',
    ],

    'persons'       => [
        'name'       => 'Person',
        'repository' => 'Webkul\Contact\Repositories\PersonRepository',
    ],

    'organizations' => [
        'name'       => 'Organization',
        'repository' => 'Webkul\Contact\Repositories\OrganizationRepository',
    ],

    'products'      => [
        'name'       => 'Product',
        'repository' => 'Webkul\Product\Repositories\ProductRepository',
    ],

    'quotes'      => [
        'name'       => 'Quote',
        'repository' => 'Webkul\Quote\Repositories\QuoteRepository',
    ],
];