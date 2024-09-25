<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.index.general.title',
        'info' => 'admin::app.configuration.index.general.info',
        'sort' => 1,
    ], [
        'key'  => 'general.general',
        'name' => 'admin::app.configuration.index.general.general.title',
        'info' => 'admin::app.configuration.index.general.general.info',
        'icon' => 'icon-setting',
        'sort' => 1,
    ], [
        'key'    => 'general.general.locale_settings',
        'name'   => 'admin::app.configuration.index.general.general.locale-settings.title',
        'info'   => 'admin::app.configuration.index.general.general.locale-settings.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'locale',
                'title'   => 'admin::app.configuration.index.general.general.locale-settings.title',
                'type'    => 'select',
                'default' => 'en',
                'options' => 'Webkul\Core\Core@locales',
            ],
        ],
    ],
    [
        'key'    => 'general.general.currency-settings',
        'name'   => 'admin::app.configuration.index.general.general.currency-settings.title',
        'info'   => 'admin::app.configuration.index.general.general.currency-settings.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'currency',
                'title'   => 'admin::app.configuration.index.general.general.currency-settings.title',
                'type'    => 'select',
                'default' => 'USD',
                'options' => 'Webkul\Core\Core@retrieveCurrencies',
            ],
        ],
    ],
];
