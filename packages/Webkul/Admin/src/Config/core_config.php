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
    ],
];