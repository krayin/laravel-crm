<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.general',
        'sort' => 1,
    ], [
        'key'  => 'general.general',
        'name' => 'admin::app.configuration.general',
        'sort' => 1,
    ], [
        'key'    => 'general.general.locale',
        'name'   => 'admin::app.configuration.general',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'locale',
                'title'         => 'admin::app.configuration.locale',
                'type'          => 'select',
                'options'       => [
                    [
                        'title' => 'English',
                        'value' => 'en',
                    ], [
                        'title' => 'Arabic',
                        'value' => 'ar',
                    ],
                ],
            ],
        ],
    ], [
        'key'  => 'emails',
        'name' => 'admin::app.configuration.emails.email',
        'sort' => 1,
    ], [
        'key'  => 'emails.general',
        'name' => 'admin::app.configuration.emails.notification_label',
        'sort' => 1,
    ], [
        'key'    => 'emails.general.notifications',
        'name'   => 'admin::app.configuration.emails.notification_label',
        'sort'   => 1,
        'fields' => [
            [
                'name'  => 'emails.general.notifications.new_lead',
                'title' => 'admin::app.configuration.emails.new_lead',
                'type'  => 'boolean',
            ],
        ],
    ],
];