<?php

return [
    [
        'key'   => 'dashboard',
        'name'  => 'admin::app.layouts.dashboard',
        'route' => 'admin.dashboard.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings',
        'name'  => 'admin::app.acl.settings',
        'route' => 'admin.settings.users.index',
        'sort'  => 2,
    ],  [
        'key'   => 'settings.users',
        'name'  => 'admin::app.acl.users',
        'route' => 'admin.settings.users.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.users.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.settings.users.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.users.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.roles',
        'name'  => 'admin::app.acl.roles',
        'route' => 'admin.settings.roles.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.roles.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.settings.roles.create',
        'sort'  => 1,
    ], [
        'key'   => 'settings.roles.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.settings.roles.edit',
        'sort'  => 2,
    ], [
        'key'   => 'settings.roles.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.roles.delete',
        'sort'  => 3,
    ],
];

?>