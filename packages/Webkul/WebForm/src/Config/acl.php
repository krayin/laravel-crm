<?php

return [
    [
        'key'   => 'settings.other_settings.web_forms',
        'name'  => 'web_form::app.acl.title',
        'route' => 'admin.settings.web_forms.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.other_settings.web_forms.view',
        'name'  => 'web_form::app.acl.view',
        'route' => 'admin.settings.web_forms.view',
        'sort'  => 1,
    ], [
        'key'   => 'settings.other_settings.web_forms.create',
        'name'  => 'web_form::app.acl.create',
        'route' => ['admin.settings.web_forms.create', 'admin.settings.web_forms.store'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.other_settings.web_forms.edit',
        'name'  => 'web_form::app.acl.edit',
        'route' => ['admin.settings.web_forms.edit', 'admin.settings.web_forms.update'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.other_settings.web_forms.delete',
        'name'  => 'web_form::app.acl.delete',
        'route' => 'admin.settings.web_forms.delete',
        'sort'  => 4,
    ],
];
