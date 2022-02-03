<?php

return [
    [
        'key'   => 'settings.other_settings.web_forms',
        'name'  => 'web_form::app.title',
        'route' => 'admin.settings.web_forms.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.other_settings.web_forms.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.settings.web_forms.view',
        'sort'  => 1
    ], [
        'key'   => 'settings.other_settings.web_forms.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.web_forms.create', 'admin.settings.web_forms.store'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.other_settings.web_forms.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.web_forms.edit', 'admin.settings.web_forms.update'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.other_settings.web_forms.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.web_forms.delete',
        'sort'  => 4,
    ]
];