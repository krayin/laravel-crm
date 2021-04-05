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
        'route'      => 'admin.contacts.customers.index',
        'sort'       => 3,
        'icon-class' => 'phone-icon',
    ], [
        'key'        => 'contacts.customers',
        'name'       => 'admin::app.layouts.customers',
        'route'      => 'admin.contacts.customers.index',
        'sort'       => 1,
    ], [
        'key'        => 'contacts.companies',
        'name'       => 'admin::app.layouts.companies',
        'route'      => 'admin.contacts.companies.index',
        'sort'       => 2,
    ], [
        'key'        => 'settings',
        'name'       => 'admin::app.layouts.settings',
        'route'      => 'admin.settings.users.index',
        'sort'       => 3,
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
    ]
];