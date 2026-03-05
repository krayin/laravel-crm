<?php

return [
    /**
     * Dashboard.
     */
    [
        'key'        => 'dashboard',
        'name'       => 'admin::app.layouts.dashboard',
        'route'      => 'admin.dashboard.index',
        'sort'       => 1,
        'icon-class' => 'icon-dashboard',
    ],

    /**
     * Leads.
     */
    [
        'key'        => 'leads',
        'name'       => 'admin::app.layouts.leads',
        'route'      => 'admin.leads.index',
        'sort'       => 2,
        'icon-class' => 'icon-leads',
    ],

    /**
     * Quotes.
     */
    [
        'key'        => 'quotes',
        'name'       => 'admin::app.layouts.quotes',
        'route'      => 'admin.quotes.index',
        'sort'       => 3,
        'icon-class' => 'icon-quote',
    ],

    /**
     * Emails.
     */
    [
        'key'        => 'mail',
        'name'       => 'admin::app.layouts.mail.title',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'inbox'],
        'sort'       => 4,
        'icon-class' => 'icon-mail',
    ], [
        'key'        => 'mail.inbox',
        'name'       => 'admin::app.layouts.mail.inbox',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'inbox'],
        'sort'       => 2,
        'icon-class' => '',
    ], [
        'key'        => 'mail.draft',
        'name'       => 'admin::app.layouts.mail.draft',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'draft'],
        'sort'       => 3,
        'icon-class' => '',
    ], [
        'key'        => 'mail.outbox',
        'name'       => 'admin::app.layouts.mail.outbox',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'outbox'],
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'mail.sent',
        'name'       => 'admin::app.layouts.mail.sent',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'sent'],
        'sort'       => 4,
        'icon-class' => '',
    ], [
        'key'        => 'mail.trash',
        'name'       => 'admin::app.layouts.mail.trash',
        'route'      => 'admin.mail.index',
        'params'     => ['route' => 'trash'],
        'sort'       => 5,
        'icon-class' => '',
    ],
    // , [
    //     'key'        => 'mail.setting',
    //     'name'       => 'admin::app.layouts.mail.setting',
    //     'route'      => 'admin.mail.index',
    //     'params'     => ['route' => 'setting'],
    //     'sort'       => 5,
    // ]

    /**
     * Activities.
     */
    [
        'key'        => 'activities',
        'name'       => 'admin::app.layouts.activities',
        'route'      => 'admin.activities.index',
        'sort'       => 5,
        'icon-class' => 'icon-activity',
    ],

    /**
     * Contacts.
     */
    [
        'key'        => 'contacts',
        'name'       => 'admin::app.layouts.contacts',
        'route'      => 'admin.contacts.persons.index',
        'sort'       => 6,
        'icon-class' => 'icon-contact',
    ], [
        'key'        => 'contacts.persons',
        'name'       => 'admin::app.layouts.persons',
        'route'      => 'admin.contacts.persons.index',
        'sort'       => 1,
        'icon-class' => '',
    ], [
        'key'        => 'contacts.organizations',
        'name'       => 'admin::app.layouts.organizations',
        'route'      => 'admin.contacts.organizations.index',
        'sort'       => 2,
        'icon-class' => '',
    ],

    /**
     * Products.
     */
    [
        'key'        => 'products',
        'name'       => 'admin::app.layouts.products',
        'route'      => 'admin.products.index',
        'sort'       => 7,
        'icon-class' => 'icon-product',
    ],

    /**
     * Settings.
     */
    [
        'key'        => 'settings',
        'name'       => 'admin::app.layouts.settings',
        'route'      => 'admin.settings.index',
        'sort'       => 8,
        'icon-class' => 'icon-setting',
    ], [
        'key'        => 'settings.user',
        'name'       => 'admin::app.layouts.user',
        'route'      => 'admin.settings.groups.index',
        'info'       => 'admin::app.layouts.user-info',
        'sort'       => 1,
        'icon-class' => 'icon-settings-group',
    ], [
        'key'        => 'settings.user.groups',
        'name'       => 'admin::app.layouts.groups',
        'info'       => 'admin::app.layouts.groups-info',
        'route'      => 'admin.settings.groups.index',
        'sort'       => 1,
        'icon-class' => 'icon-settings-group',
    ], [
        'key'        => 'settings.user.roles',
        'name'       => 'admin::app.layouts.roles',
        'info'       => 'admin::app.layouts.roles-info',
        'route'      => 'admin.settings.roles.index',
        'sort'       => 2,
        'icon-class' => 'icon-role',
    ], [
        'key'        => 'settings.user.users',
        'name'       => 'admin::app.layouts.users',
        'info'       => 'admin::app.layouts.users-info',
        'route'      => 'admin.settings.users.index',
        'sort'       => 3,
        'icon-class' => 'icon-user',
    ], [
        'key'        => 'settings.lead',
        'name'       => 'admin::app.layouts.lead',
        'info'       => 'admin::app.layouts.lead-info',
        'route'      => 'admin.settings.pipelines.index',
        'sort'       => 2,
        'icon-class' => '',
    ], [
        'key'        => 'settings.lead.pipelines',
        'name'       => 'admin::app.layouts.pipelines',
        'info'       => 'admin::app.layouts.pipelines-info',
        'route'      => 'admin.settings.pipelines.index',
        'sort'       => 1,
        'icon-class' => 'icon-settings-pipeline',
    ], [
        'key'        => 'settings.lead.sources',
        'name'       => 'admin::app.layouts.sources',
        'info'       => 'admin::app.layouts.sources-info',
        'route'      => 'admin.settings.sources.index',
        'sort'       => 2,
        'icon-class' => 'icon-settings-sources',
    ], [
        'key'        => 'settings.lead.types',
        'name'       => 'admin::app.layouts.types',
        'info'       => 'admin::app.layouts.types-info',
        'route'      => 'admin.settings.types.index',
        'sort'       => 3,
        'icon-class' => 'icon-settings-type',
    ], [
        'key'        => 'settings.warehouse',
        'name'       => 'admin::app.layouts.warehouse',
        'info'       => 'admin::app.layouts.warehouses-info',
        'route'      => 'admin.settings.pipelines.index',
        'icon-class' => '',
        'sort'       => 2,
    ], [
        'key'        => 'settings.warehouse.warehouses',
        'name'       => 'admin::app.layouts.warehouses',
        'info'       => 'admin::app.layouts.warehouses-info',
        'route'      => 'admin.settings.warehouses.index',
        'sort'       => 1,
        'icon-class' => 'icon-settings-warehouse',
    ], [
        'key'        => 'settings.automation',
        'name'       => 'admin::app.layouts.automation',
        'info'       => 'admin::app.layouts.automation-info',
        'route'      => 'admin.settings.attributes.index',
        'sort'       => 3,
        'icon-class' => '',
    ], [
        'key'        => 'settings.automation.attributes',
        'name'       => 'admin::app.layouts.attributes',
        'info'       => 'admin::app.layouts.attributes-info',
        'route'      => 'admin.settings.attributes.index',
        'sort'       => 1,
        'icon-class' => 'icon-attribute',
    ], [
        'key'        => 'settings.automation.email_templates',
        'name'       => 'admin::app.layouts.email-templates',
        'info'       => 'admin::app.layouts.email-templates-info',
        'route'      => 'admin.settings.email_templates.index',
        'sort'       => 2,
        'icon-class' => 'icon-settings-mail',
    ], [
        'key'        => 'settings.automation.events',
        'name'       => 'admin::app.layouts.events',
        'info'       => 'admin::app.layouts.events-info',
        'route'      => 'admin.settings.marketing.events.index',
        'sort'       => 2,
        'icon-class' => 'icon-calendar',
    ], [
        'key'        => 'settings.automation.campaigns',
        'name'       => 'admin::app.layouts.campaigns',
        'info'       => 'admin::app.layouts.campaigns-info',
        'route'      => 'admin.settings.marketing.campaigns.index',
        'sort'       => 2,
        'icon-class' => 'icon-note',
    ], [
        'key'        => 'settings.automation.webhooks',
        'name'       => 'admin::app.layouts.webhooks',
        'info'       => 'admin::app.layouts.webhooks-info',
        'route'      => 'admin.settings.webhooks.index',
        'sort'       => 2,
        'icon-class' => 'icon-settings-webhooks',
    ], [
        'key'        => 'settings.automation.workflows',
        'name'       => 'admin::app.layouts.workflows',
        'info'       => 'admin::app.layouts.workflows-info',
        'route'      => 'admin.settings.workflows.index',
        'sort'       => 3,
        'icon-class' => 'icon-settings-flow',
    ],
    [
        'key'        => 'settings.automation.data_transfer',
        'name'       => 'admin::app.layouts.data_transfer',
        'info'       => 'admin::app.layouts.data_transfer_info',
        'route'      => 'admin.settings.data_transfer.imports.index',
        'sort'       => 4,
        'icon-class' => 'icon-download',
    ], [
        'key'        => 'settings.other_settings',
        'name'       => 'admin::app.layouts.other-settings',
        'info'       => 'admin::app.layouts.other-settings-info',
        'route'      => 'admin.settings.tags.index',
        'sort'       => 4,
        'icon-class' => 'icon-settings',
    ], [
        'key'        => 'settings.other_settings.tags',
        'name'       => 'admin::app.layouts.tags',
        'info'       => 'admin::app.layouts.tags-info',
        'route'      => 'admin.settings.tags.index',
        'sort'       => 1,
        'icon-class' => 'icon-settings-tag',
    ],

    /**
     * Configuration.
     */
    [
        'key'        => 'configuration',
        'name'       => 'admin::app.layouts.configuration',
        'route'      => 'admin.configuration.index',
        'sort'       => 9,
        'icon-class' => 'icon-configuration',
    ],

];
