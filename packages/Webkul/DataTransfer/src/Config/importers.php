<?php

return [
    'persons' => [
        'title'       => 'data_transfer::app.importers.persons.title',
        'importer'    => 'Webkul\DataTransfer\Helpers\Importers\Person\Importer',
        'sample_path' => 'data-transfer/samples/persons.csv',
    ],

    'products' => [
        'title'       => 'data_transfer::app.importers.products.title',
        'importer'    => 'Webkul\DataTransfer\Helpers\Importers\Product\Importer',
        'sample_path' => 'data-transfer/samples/products.csv',
    ],

    //     'tax_rates' => [
    //         'title'       => 'data_transfer::app.importers.tax-rates.title',
    //         'importer'    => 'Webkul\DataTransfer\Helpers\Importers\TaxRate\Importer',
    //         'sample_path' => 'data-transfer/samples/tax-rates.csv',
    //     ],
];
