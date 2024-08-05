<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.general',
        'info' => 'admin::app.configuration.general',
        'sort' => 1,
    ], [
        'key'    => 'general.locale_settings',
        'name'   => 'admin::app.configuration.locale-settings',
        'icon'   => 'icon-eye',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'locale',
                'title'      => 'admin::app.configuration.locale',
                'type'       => 'select',
                'validation' => 'required',
                'options'    => 'Webkul\Core\Core@locales',
            ],
        ],
    ],
];
