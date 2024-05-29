<?php

return [
    [
        'key' => 'general.whatsapp',
        'name' => 'whatsapp::app.configuration.whatsapp.title',
        'sort' => 2,
        'fields' => [
            [
                'name' => 'status',
                'title' => 'whatsapp::app.configuration.whatsapp.status',
                'type' => 'boolean'
            ],
            [
                'name' => 'phone_no_id',
                'title' => 'whatsapp::app.configuration.whatsapp.phone-number-id',
                'type' => 'text',
            ],
            [
                'name' => 'access_token',
                'title' => 'whatsapp::app.configuration.whatsapp.access-token',
                'type' => 'textarea',
            ],
        ],
    ],
];
