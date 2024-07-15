<?php

return [
    'leads'         => [
        'name'       => 'admin::app.leads.title',
        'repository' => 'Webkul\Lead\Repositories\LeadRepository',
    ],

    'persons'       => [
        'name'       => 'admin::app.contacts.persons.title',
        'repository' => 'Webkul\Contact\Repositories\PersonRepository',
    ],

    'organizations' => [
        'name'       => 'admin::app.contacts.organizations.title',
        'repository' => 'Webkul\Contact\Repositories\OrganizationRepository',
    ],

    'products'      => [
        'name'       => 'admin::app.products.title',
        'repository' => 'Webkul\Product\Repositories\ProductRepository',
    ],

    'quotes'      => [
        'name'       => 'admin::app.quotes.title',
        'repository' => 'Webkul\Quote\Repositories\QuoteRepository',
    ],

    'warehouses'      => [
        'name'       => 'admin::app.settings.warehouses.title',
        'repository' => 'Webkul\Warehouse\Repositories\WarehouseRepository',
    ],
];