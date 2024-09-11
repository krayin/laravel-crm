<?php

return [
    'importers' => [
        'persons' => [
            'title' => 'Persons',

            'validation' => [
                'errors' => [
                    'duplicate-email' => 'Email : \'%s\' is found more than once in the import file.',
                    'duplicate-phone' => 'Phone : \'%s\' is found more than once in the import file.',
                    'email-not-found' => 'Email : \'%s\' not found in the system.',
                ],
            ],
        ],

        'products' => [
            'title' => 'Products',

            'validation' => [
                'errors' => [
                    'sku-not-found' => 'Product with specified SKU not found',
                ],
            ],
        ],

        'leads' => [
            'title' => 'Leads',

            'validation' => [
                'errors' => [
                    'id-not-found' => 'ID : \'%s\' not found in the system.',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => 'Columns number "%s" have empty headers.',
            'column-name-invalid'  => 'Invalid column names: "%s".',
            'column-not-found'     => 'Required columns not found: %s.',
            'column-numbers'       => 'Number of columns does not correspond to the number of rows in the header.',
            'invalid-attribute'    => 'Header contains invalid attribute(s): "%s".',
            'system'               => 'An unexpected system error occurred.',
            'wrong-quotes'         => 'Curly quotes used instead of straight quotes.',
            'already-exists'       => 'The :attribute already exists.',
        ],
    ],
];
