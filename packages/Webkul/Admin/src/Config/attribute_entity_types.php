<?php

return [
    'leads'         => [
        'name'       => 'admin::app.leads.index.title',
        'repository' => 'Webkul\Lead\Repositories\LeadRepository',
    ],

    'persons'       => [
        'name'       => 'admin::app.contacts.persons.index.title',
        'repository' => 'Webkul\Contact\Repositories\PersonRepository',
    ],

    'organizations' => [
        'name'       => 'admin::app.contacts.organizations.index.title',
        'repository' => 'Webkul\Contact\Repositories\OrganizationRepository',
    ],

    'products'      => [
        'name'       => 'admin::app.products.index.title',
        'repository' => 'Webkul\Product\Repositories\ProductRepository',
    ],

    'quotes'      => [
        'name'       => 'admin::app.quotes.index.title',
        'repository' => 'Webkul\Quote\Repositories\QuoteRepository',
    ],

    'warehouses'      => [
        'name'       => 'admin::app.settings.warehouses.index.title',
        'repository' => 'Webkul\Warehouse\Repositories\WarehouseRepository',
    ],
];
