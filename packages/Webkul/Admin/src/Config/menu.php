<?php

return [
    [
        'key'        => 'dashboard',
        'name'       => 'admin::app.layouts.dashboard',
        'route'      => 'admin.dashboard.index',
        'sort'       => 1,
        'icon-class' => 'dashboard-icon',
    ], [
        'key'        => 'leads',
        'name'       => 'admin::app.layouts.leads',
        'route'      => 'admin.leads.index',
        'sort'       => 2,
        'icon-class' => 'leads-icon',
    ], [
        'key'        => 'contacts',
        'name'       => 'admin::app.layouts.contacts',
        'route'      => 'admin.contacts.persons.index',
        'sort'       => 3,
        'icon-class' => 'phone-icon',
    ], [
        'key'        => 'contacts.persons',
        'name'       => 'admin::app.layouts.persons',
        'route'      => 'admin.contacts.persons.index',
        'sort'       => 1,
    ], [
        'key'        => 'contacts.organizations',
        'name'       => 'admin::app.layouts.organizations',
        'route'      => 'admin.contacts.organizations.index',
        'sort'       => 2,
    ], [
        'key'        => 'products',
        'name'       => 'admin::app.layouts.products',
        'route'      => 'admin.products.index',
        'sort'       => 3,
        'icon-class' => 'products-icon',
    ], [
        'key'        => 'settings',
        'name'       => 'admin::app.layouts.settings',
        'route'      => 'admin.settings.users.index',
        'sort'       => 4,
        'icon-class' => 'settings-icon',
    ], [
        'key'        => 'settings.roles',
        'name'       => 'admin::app.layouts.roles',
        'route'      => 'admin.settings.roles.index',
        'sort'       => 1,
    ], [
        'key'        => 'settings.users',
        'name'       => 'admin::app.layouts.users',
        'route'      => 'admin.settings.users.index',
        'sort'       => 2,
    ], [
        'key'        => 'settings.attributes',
        'name'       => 'admin::app.layouts.attributes',
        'route'      => 'admin.settings.attributes.index',
        'sort'       => 3,
    ], [
        'key'        => 'configuration',
        'name'       => 'admin::app.layouts.configuration',
        'route'      => 'admin.configuration.index',
        'sort'       => 4,
        'icon-class' => 'tools-icon',
    ]
];