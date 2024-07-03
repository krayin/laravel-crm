<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.general',
        'info' => 'admin::app.configuration.locale-settings',
        'sort' => 1,
    ], [
        'key'    => 'general.locale_settings',
        'name'   => 'admin::app.configuration.locale-settings',
        'info'   => 'admin::app.configuration.locale-settings',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'locale',
                'title'   => 'admin::app.configuration.locale',
                'type'    => 'select',
                'options' => 'Webkul\Core\Core@locales'
            ],
        ],
    ], [
        'key'    => 'general.shipping',
        'name'   => 'Shipping',
        'info'   => 'Shipping',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'shipping',
                'title'   => 'Shipping',
                'type'    => 'select',
                'options' => [
                    [
                        'value'    => 1,
                        'title' => 'Flat Rate'
                    ],
                    [
                        'value'    => 2,
                        'title' => 'Test 2'
                    ]
                ]
            ],
        ],
    ], [
        'key'  => 'test',
        'name' => 'Test',
        'info' => 'admin::app.configuration.locale-settings',
        'sort' => 1,
    ], [
        'key'    => 'test.settings',
        'name'   => 'Settings',
        'info'   => 'admin::app.configuration.locale-settings',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'locale',
                'title'   => 'Test',
                'type'    => 'multiselect',
                'options' => [
                    [
                        'value'    => 1,
                        'title' => 'Test 1'
                    ],
                    [
                        'value'    => 2,
                        'title' => 'Test 2'
                    ]
                ]
            ],
            [
                'name'    => 'title',
                'title'   => 'Order Title',
                'type'    => 'textarea',
                'default' => 'Get UPTO 40% OFF on your 1st order',
            ],
        ],
    ],
];