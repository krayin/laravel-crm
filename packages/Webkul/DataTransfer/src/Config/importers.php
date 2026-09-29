<?php

return [
    'persons' => [
        'title' => 'admin::app.settings.data-transfer.importers.persons.title',
        'importer' => 'Webkul\DataTransfer\Helpers\Importers\Persons\Importer',
        'sample_path' => 'data-transfer/samples/persons.csv',
    ],

    'products' => [
        'title' => 'admin::app.settings.data-transfer.importers.products.title',
        'importer' => 'Webkul\DataTransfer\Helpers\Importers\Products\Importer',
        'sample_path' => 'data-transfer/samples/products.csv',
    ],

    'leads' => [
        'title' => 'admin::app.settings.data-transfer.importers.leads.title',
        'importer' => 'Webkul\DataTransfer\Helpers\Importers\Leads\Importer',
        'sample_path' => 'data-transfer/samples/leads.csv',
    ],
];
