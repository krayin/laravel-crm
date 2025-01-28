<?php

return [
    [
        'key'  => 'general',
        'name' => 'admin::app.configuration.index.general.title',
        'info' => 'admin::app.configuration.index.general.info',
        'sort' => 1,
    ], [
        'key'  => 'general.general',
        'name' => 'admin::app.configuration.index.general.general.title',
        'info' => 'admin::app.configuration.index.general.general.info',
        'icon' => 'icon-setting',
        'sort' => 1,
    ], [
        'key'    => 'general.general.locale_settings',
        'name'   => 'admin::app.configuration.index.general.general.locale-settings.title',
        'info'   => 'admin::app.configuration.index.general.general.locale-settings.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'locale',
                'title'   => 'admin::app.configuration.index.general.general.locale-settings.title',
                'type'    => 'select',
                'default' => 'en',
                'options' => 'Webkul\Core\Core@locales',
            ],
        ],
    ], [
        'key'    => 'general.general.admin_logo',
        'name'   => 'admin::app.configuration.index.general.general.admin-logo.title',
        'info'   => 'admin::app.configuration.index.general.general.admin-logo.title-info',
        'sort'   => 2,
        'fields' => [
            [
                'name'          => 'logo_image',
                'title'         => 'admin::app.configuration.index.general.general.admin-logo.logo-image',
                'type'          => 'image',
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp,svg',
            ],
        ],
    ], [
        'key'    => 'general.settings',
        'name'   => 'admin::app.configuration.index.general.settings.title',
        'info'   => 'admin::app.configuration.index.general.settings.info',
        'icon'   => 'icon-configuration',
        'sort'   => 1,
    ], [
        'key'    => 'general.settings.footer',
        'name'   => 'admin::app.configuration.index.general.settings.footer.title',
        'info'   => 'admin::app.configuration.index.general.settings.footer.info',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'powered_by',
                'title'      => 'admin::app.configuration.index.general.settings.footer.powered-by',
                'type'       => 'text',
                'default'    => 'Powered by',
                'validation' => 'max:50',
            ], [
                'name'       => 'powered_by_redirection_title',
                'title'      => 'admin::app.configuration.index.general.settings.footer.powered-by-redirection-title',
                'type'       => 'text',
                'default'    => 'Krayin',
                'validation' => 'max:20',
            ], [
                'name'    => 'powered_by_redirection_link',
                'title'   => 'admin::app.configuration.index.general.settings.footer.powered-by-redirection-link',
                'default' => 'https://krayincrm.com',
                'type'    => 'text',
            ], [
                'name'       => 'other',
                'title'      => 'admin::app.configuration.index.general.settings.footer.other',
                'type'       => 'text',
                'default'    => 'an open-source project by',
                'validation' => 'max:100',
            ], [
                'name'       => 'other_redirection_title',
                'title'      => 'admin::app.configuration.index.general.settings.footer.other-redirection-title',
                'type'       => 'text',
                'default'    => 'Webkul',
                'validation' => 'max:25',
            ], [
                'name'    => 'other_redirection_link',
                'title'   => 'admin::app.configuration.index.general.settings.footer.other-redirection-link',
                'default' => 'https://webkul.com',
                'type'    => 'text',
            ],
        ],
    ], [
        'key'    => 'general.settings.menu',
        'name'   => 'admin::app.configuration.index.general.settings.menu.title',
        'info'   => 'admin::app.configuration.index.general.settings.menu.info',
        'sort'   => 2,
        'fields' => [
            [
                'name'       => 'dashboard',
                'title'      => 'admin::app.configuration.index.general.settings.menu.dashboard',
                'type'       => 'text',
                'default'    => 'Dashboard',
                'validation' => 'max:20',
            ], [
                'name'       => 'leads',
                'title'      => 'admin::app.configuration.index.general.settings.menu.leads',
                'type'       => 'text',
                'default'    => 'Leads',
                'validation' => 'max:20',
            ], [
                'name'       => 'quotes',
                'title'      => 'admin::app.configuration.index.general.settings.menu.quotes',
                'type'       => 'text',
                'default'    => 'Quotes',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.mail',
                'title'      => 'admin::app.configuration.index.general.settings.menu.mail',
                'type'       => 'text',
                'default'    => 'Mail',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.inbox',
                'title'      => 'admin::app.configuration.index.general.settings.menu.inbox',
                'type'       => 'text',
                'default'    => 'Inbox',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.draft',
                'title'      => 'admin::app.configuration.index.general.settings.menu.draft',
                'type'       => 'text',
                'default'    => 'Draft',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.outbox',
                'title'      => 'admin::app.configuration.index.general.settings.menu.outbox',
                'type'       => 'text',
                'default'    => 'Outbox',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.sent',
                'title'      => 'admin::app.configuration.index.general.settings.menu.sent',
                'type'       => 'text',
                'default'    => 'Sent',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.trash',
                'title'      => 'admin::app.configuration.index.general.settings.menu.trash',
                'type'       => 'text',
                'default'    => 'Trash',
                'validation' => 'max:20',
            ], [
                'name'       => 'activities',
                'title'      => 'admin::app.configuration.index.general.settings.menu.activities',
                'type'       => 'text',
                'default'    => 'Activities',
                'validation' => 'max:20',
            ], [
                'name'       => 'contacts.contacts',
                'title'      => 'admin::app.configuration.index.general.settings.menu.contacts',
                'type'       => 'text',
                'default'    => 'Contacts',
                'validation' => 'max:20',
            ], [
                'name'       => 'contacts.persons',
                'title'      => 'admin::app.configuration.index.general.settings.menu.persons',
                'type'       => 'text',
                'default'    => 'Persons',
                'validation' => 'max:20',
            ], [
                'name'       => 'contacts.organizations',
                'title'      => 'admin::app.configuration.index.general.settings.menu.organizations',
                'type'       => 'text',
                'default'    => 'Organizations',
                'validation' => 'max:20',
            ], [
                'name'       => 'products',
                'title'      => 'admin::app.configuration.index.general.settings.menu.products',
                'type'       => 'text',
                'default'    => 'Products',
                'validation' => 'max:20',
            ], [
                'name'       => 'settings',
                'title'      => 'admin::app.configuration.index.general.settings.menu.settings',
                'type'       => 'text',
                'default'    => 'Settings',
                'validation' => 'max:20',
            ], [
                'name'       => 'configuration',
                'title'      => 'admin::app.configuration.index.general.settings.menu.configuration',
                'type'       => 'text',
                'default'    => 'Configuration',
                'validation' => 'max:20',
            ],
        ],
    ], [
        'key'    => 'general.settings.menu_color',
        'name'   => 'admin::app.configuration.index.general.settings.menu-color.title',
        'info'   => 'admin::app.configuration.index.general.settings.menu-color.info',
        'sort'   => 2,
        'fields' => [
            [
                'name'       => 'active_background_color',
                'title'      => 'admin::app.configuration.index.general.settings.menu-color.active-background-color',
                'type'       => 'color',
                'default'    => '#0E90D9',
            ], [
                'name'       => 'active_text_color',
                'title'      => 'admin::app.configuration.index.general.settings.menu-color.active-text-color',
                'type'       => 'color',
                'default'    => '#ffffff',
            ], [
                'name'       => 'text_color',
                'title'      => 'admin::app.configuration.index.general.settings.menu-color.text-color',
                'type'       => 'color',
                'default'    => '#757575',
            ],
        ],
    ],
];
