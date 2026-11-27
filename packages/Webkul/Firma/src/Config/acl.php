<?php

return [
    [
        'key' => 'leads.firma',
        'name' => 'admin::app.firma.acl.send',
        'route' => [
            'admin.leads.firma.index',
            'admin.leads.firma.templates',
            'admin.leads.firma.store',
            'admin.leads.firma.refresh',
        ],
        'sort' => 5,
    ], [
        'key' => 'settings.other_settings.firma',
        'name' => 'admin::app.firma.acl.settings',
        'route' => [
            'admin.settings.firma.index',
            'admin.settings.firma.update',
            'admin.settings.firma.test',
            'admin.settings.firma.register_webhook',
        ],
        'sort' => 3,
    ],
];
