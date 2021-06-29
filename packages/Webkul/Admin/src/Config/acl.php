<?php

return [
    [
        'key'   => 'dashboard',
        'name'  => 'admin::app.layouts.dashboard',
        'route' => 'admin.dashboard.index',
        'sort'  => 1,
    ],  [
        'key'   => 'leads',
        'name'  => 'admin::app.acl.leads',
        'route' => 'admin.leads.index',
        'sort'  => 2,
    ], [
        'key'   => 'leads.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.leads.store',
        'sort'  => 1,
    ], [
        'key'   => 'leads.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.leads.update',
        'sort'  => 2,
    ], [
        'key'   => 'leads.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.leads.delete',
        'sort'  => 3,
    ],  [
        'key'   => 'mail',
        'name'  => 'admin::app.acl.mail',
        'route' => 'admin.mail.index',
        'sort'  => 3,
    ], [
        'key'   => 'mail.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.mail.view',
        'sort'  => 1,
    ], [
        'key'   => 'mail.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.mail.store',
        'sort'  => 2,
    ], [
        'key'   => 'mail.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.mail.update',
        'sort'  => 3,
    ], [
        'key'   => 'mail.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.mail.delete', 'admin.mail.mass-delete'],
        'sort'  => 4,
    ], [
        'key'   => 'activities',
        'name'  => 'admin::app.acl.activities',
        'route' => 'admin.activities.index',
        'sort'  => 4,
    ], [
        'key'   => 'activities.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.activities.store',
        'sort'  => 1,
    ], [
        'key'   => 'activities.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => 'admin.activities.update',
        'sort'  => 2,
    ], [
        'key'   => 'activities.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.activities.delete', 'admin.activities.mass-delete'],
        'sort'  => 3,
    ], [
        'key'   => 'contacts',
        'name'  => 'admin::app.acl.contacts',
        'route' => 'admin.contacts.users.index',
        'sort'  => 5,
    ],  [
        'key'   => 'contacts.persons',
        'name'  => 'admin::app.acl.persons',
        'route' => 'admin.contacts.persons.index',
        'sort'  => 1,
    ], [
        'key'   => 'contacts.persons.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.contacts.persons.store',
        'sort'  => 2,
    ], [
        'key'   => 'contacts.persons.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.contacts.persons.edit', 'admin.contacts.persons.update'],
        'sort'  => 3,
    ], [
        'key'   => 'contacts.persons.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.contacts.persons.delete', 'admin.contacts.persons.mass-delete'],
        'sort'  => 4,
    ],  [
        'key'   => 'contacts.organizations',
        'name'  => 'admin::app.acl.organizations',
        'route' => 'admin.contacts.organizations.index',
        'sort'  => 2,
    ], [
        'key'   => 'contacts.organizations.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.contacts.organizations.store',
        'sort'  => 1,
    ], [
        'key'   => 'contacts.organizations.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.contacts.organizations.edit', 'admin.contacts.organizations.update'],
        'sort'  => 2,
    ], [
        'key'   => 'contacts.organizations.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.contacts.organizations.delete', 'admin.contacts.organizations.mass-delete'],
        'sort'  => 3,
    ],  [
        'key'   => 'products',
        'name'  => 'admin::app.acl.products',
        'route' => 'admin.products.index',
        'sort'  => 6,
    ], [
        'key'   => 'products.create',
        'name'  => 'admin::app.acl.create',
        'route' => 'admin.products.store',
        'sort'  => 1,
    ], [
        'key'   => 'products.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.products.edit', 'admin.products.update'],
        'sort'  => 2,
    ], [
        'key'   => 'products.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.products.delete', 'admin.products.mass-delete'],
        'sort'  => 3,
    ], [
        'key'   => 'settings',
        'name'  => 'admin::app.acl.settings',
        'route' => 'admin.settings.users.index',
        'sort'  => 7,
    ],  [
        'key'   => 'settings.users',
        'name'  => 'admin::app.acl.users',
        'route' => 'admin.settings.users.index',
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.users.create', 'admin.settings.users.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.users.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.users.edit', 'admin.settings.users.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.users.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => ['admin.settings.users.delete', 'admin.settings.users.mass-delete'],
        'sort'  => 3,
    ], [
        'key'   => 'settings.roles',
        'name'  => 'admin::app.acl.roles',
        'route' => 'admin.settings.roles.index',
        'sort'  => 2,
    ], [
        'key'   => 'settings.roles.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.roles.create', 'admin.settings.roles.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.roles.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.roles.edit', 'admin.settings.roles.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.roles.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.roles.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.attributes',
        'name'  => 'admin::app.acl.attributes',
        'route' => 'admin.settings.attributes.index',
        'sort'  => 3,
    ], [
        'key'   => 'settings.attributes.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.attributes.create', 'admin.settings.attributes.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.attributes.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.attributes.edit', 'admin.settings.attributes.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.attributes.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.attributes.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.sources',
        'name'  => 'admin::app.acl.sources',
        'route' => 'admin.settings.sources.index',
        'sort'  => 3,
    ], [
        'key'   => 'settings.sources.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.sources.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.sources.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.sources.edit', 'admin.settings.sources.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.sources.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.sources.delete',
        'sort'  => 3,
    ], [
        'key'   => 'settings.types',
        'name'  => 'admin::app.acl.types',
        'route' => 'admin.settings.types.index',
        'sort'  => 3,
    ], [
        'key'   => 'settings.types.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.settings.types.store'],
        'sort'  => 1,
    ], [
        'key'   => 'settings.types.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.settings.types.edit', 'admin.settings.types.update'],
        'sort'  => 2,
    ], [
        'key'   => 'settings.types.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.settings.types.delete',
        'sort'  => 3,
    ]
];

?>