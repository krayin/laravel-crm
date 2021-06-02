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
    ],
];