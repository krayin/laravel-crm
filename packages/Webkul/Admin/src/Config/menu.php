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
        'key'        => 'quotes',
        'name'       => 'admin::app.layouts.quotes',
        'route'      => 'admin.quotes.index',
        'sort'       => 3,
        'icon-class' => 'quotes-icon',
    ], [
        'key'        => 'mail',
        'name'       => 'admin::app.layouts.mail.title',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'inbox'],
        'sort'       => 4,
        'icon-class' => 'emails-icon',
    ], [
        'key'        => 'mail.compose',
        'name'       => 'admin::app.layouts.mail.compose',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'compose'],
        'sort'       => 1,
    ], [
        'key'        => 'mail.inbox',
        'name'       => 'admin::app.layouts.mail.inbox',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'inbox'],
        'sort'       => 2,
    ], [
        'key'        => 'mail.draft',
        'name'       => 'admin::app.layouts.mail.draft',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'draft'],
        'sort'       => 3,
    ], [
        'key'        => 'mail.outbox',
        'name'       => 'admin::app.layouts.mail.outbox',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'outbox'],
        'sort'       => 4,
    ], [
        'key'        => 'mail.sent',
        'name'       => 'admin::app.layouts.mail.sent',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'sent'],
        'sort'       => 4,
    ], [
        'key'        => 'mail.trash',
        'name'       => 'admin::app.layouts.mail.trash',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'trash'],
        'sort'       => 5,
    ]
    // , [
    //     'key'        => 'mail.setting',
    //     'name'       => 'admin::app.layouts.mail.setting',
    //     'route'      => 'admin.mail.index',
    //     'params'     => ['route' => 'setting'],
    //     'sort'       => 5,
    // ]
    , [
        'key'        => 'activities',
        'name'       => 'admin::app.layouts.activities',
        'route'      => 'admin.activities.index',
        'sort'       => 5,
        'icon-class' => 'activities-icon',
    ], [
        'key'        => 'contacts',
        'name'       => 'admin::app.layouts.contacts',
        'route'      => 'admin.contacts.persons.index',
        'sort'       => 6,
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
        'sort'       => 7,
        'icon-class' => 'products-icon',
    ], [
        'key'        => 'settings',
        'name'       => 'admin::app.layouts.settings',
        'route'      => 'admin.settings.groups.index',
        'sort'       => 8,
        'icon-class' => 'settings-icon',
    ], [
        'key'        => 'settings.groups',
        'name'       => 'admin::app.layouts.groups',
        'route'      => 'admin.settings.groups.index',
        'sort'       => 1,
    ], [
        'key'        => 'settings.roles',
        'name'       => 'admin::app.layouts.roles',
        'route'      => 'admin.settings.roles.index',
        'sort'       => 2,
    ], [
        'key'        => 'settings.users',
        'name'       => 'admin::app.layouts.users',
        'route'      => 'admin.settings.users.index',
        'sort'       => 3,
    ], [
        'key'        => 'settings.attributes',
        'name'       => 'admin::app.layouts.attributes',
        'route'      => 'admin.settings.attributes.index',
        'sort'       => 4,
    ], [
        'key'        => 'settings.sources',
        'name'       => 'admin::app.layouts.sources',
        'route'      => 'admin.settings.sources.index',
        'sort'       => 5,
    ], [
        'key'        => 'settings.types',
        'name'       => 'admin::app.layouts.types',
        'route'      => 'admin.settings.types.index',
        'sort'       => 6,
    ], [
        'key'        => 'settings.email_templates',
        'name'       => 'admin::app.layouts.email-templates',
        'route'      => 'admin.settings.email_templates.index',
        'sort'       => 7,
    ], [
        'key'        => 'settings.workflows',
        'name'       => 'admin::app.layouts.workflows',
        'route'      => 'admin.settings.workflows.index',
        'sort'       => 8,
    ], [
        'key'        => 'configuration',
        'name'       => 'admin::app.layouts.configuration',
        'route'      => 'admin.configuration.index',
        'sort'       => 9,
        'icon-class' => 'tools-icon',
    ]
];