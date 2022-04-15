<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.general',
        'sort' => 1,
    ], [
        'key'    => 'general.locale_settings',
        'name'   => 'admin::app.configuration.locale-settings',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'locale',
                'title'         => 'admin::app.configuration.locale',
                'type'          => 'select',
                'data_source'   => 'Webkul\Core\Core@locales'
            ],
        ],
    ],[
        'key'    => 'general.add_locale',
        'name'   => 'Add Locale',
        'sort'   => 1,
        'validation'    => 'required',
        'fields' => [
            [
                'name'          => 'Code',
                'title'         => 'Code',
                'type'          => 'text',
            ],[
                'name'          => 'Name',
                'title'         => 'Name',
                'type'          => 'text',
            ],[
                'name'          => 'Direction',
                'title'         => 'Direction',
                'type'          => 'select',
                'options'       => [
                    [
                        'title' => 'RTL',
                        'value' => 'rtl',
                    ], [
                        'title' => 'LTR',
                        'value' => 'ltr',
                    ],
                ],
            ],
        ],
    ],
];