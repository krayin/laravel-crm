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
        'name'   => 'Admin Logo',
        'info'   => 'Set the admin logo.',
        'sort'   => 2,
        'fields' => [
            [
                'name'          => 'logo_image',
                'title'         => 'Logo image',
                'type'          => 'image',
                'channel_based' => false,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp,svg',
            ],
        ],
    ], [
        'key'    => 'general.settings',
        'name'   => 'Settings',
        'info'   => 'Settings',
        'icon'   => 'icon-configuration',
        'sort'   => 1,
    ], [
        'key'    => 'general.settings.footer',
        'name'   => 'Powered by Section Configurations',
        'info'   => 'We can configure the powered by section here',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'powered_by',
                'title'      => 'Powered By',
                'type'       => 'text',
                'default'    => 'Powered by',
                'validation' => 'max:100',
            ], [
                'name'       => 'powered_by_redirection_title',
                'title'      => 'Powered By Redirection Title',
                'type'       => 'text',
                'default'    => 'Krayin',
                'validation' => 'max:25',
            ], [
                'name'    => 'powered_by_redirection_link',
                'title'   => 'Powered By Redirection Link',
                'default' => 'https://krayincrm.com',
                'type'    => 'text',
            ], [
                'name'       => 'other',
                'title'      => 'Other',
                'type'       => 'text',
                'default'    => 'an open-source project by',
                'validation' => 'max:100',
            ], [
                'name'       => 'other_redirection_title',
                'title'      => 'Other Redirection Title',
                'type'       => 'text',
                'default'    => 'Webkul',
                'validation' => 'max:25',
            ], [
                'name'    => 'other_redirection_link',
                'title'   => 'Other Redirection Link',
                'default' => 'https://webkul.com',
                'type'    => 'text',
            ],
        ],
    ], [
        'key'    => 'general.settings.menu',
        'name'   => 'Menu Item Configurations',
        'info'   => 'We can configure the menu items name here.',
        'sort'   => 2,
        'fields' => [
            [
                'name'       => 'dashboard',
                'title'      => 'Dashboard',
                'type'       => 'text',
                'default'    => 'Dashboard',
                'validation' => 'max:20',
            ], [
                'name'       => 'leads',
                'title'      => 'Leads',
                'type'       => 'text',
                'default'    => 'Leads',
                'validation' => 'max:20',
            ], [
                'name'       => 'quotes',
                'title'      => 'Quotes',
                'type'       => 'text',
                'default'    => 'Quotes',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.mail',
                'title'      => 'Mail',
                'type'       => 'text',
                'default'    => 'Mail',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.inbox',
                'title'      => 'Inbox',
                'type'       => 'text',
                'default'    => 'Inbox',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.draft',
                'title'      => 'Draft',
                'type'       => 'text',
                'default'    => 'Draft',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.outbox',
                'title'      => 'Outbox',
                'type'       => 'text',
                'default'    => 'Outbox',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.sent',
                'title'      => 'Sent',
                'type'       => 'text',
                'default'    => 'Sent',
                'validation' => 'max:20',
            ], [
                'name'       => 'mail.trash',
                'title'      => 'Trash',
                'type'       => 'text',
                'default'    => 'Trash',
                'validation' => 'max:20',
            ], [
                'name'       => 'activities',
                'title'      => 'Activities',
                'type'       => 'text',
                'default'    => 'Activities',
                'validation' => 'max:20',
            ], [
                'name'       => 'contacts.contacts',
                'title'      => 'Contacts',
                'type'       => 'text',
                'default'    => 'Contacts',
                'validation' => 'max:20',
            ], [
                'name'       => 'contacts.persons',
                'title'      => 'Persons',
                'type'       => 'text',
                'default'    => 'Persons',
                'validation' => 'max:20',
            ], [
                'name'       => 'contacts.organizations',
                'title'      => 'Organizations',
                'type'       => 'text',
                'default'    => 'Organizations',
                'validation' => 'max:20',
            ], [
                'name'       => 'products',
                'title'      => 'Products',
                'type'       => 'text',
                'default'    => 'Products',
                'validation' => 'max:20',
            ], [
                'name'       => 'settings',
                'title'      => 'Settings',
                'type'       => 'text',
                'default'    => 'Settings',
                'validation' => 'max:20',
            ], [
                'name'       => 'configuration',
                'title'      => 'Configuration',
                'type'       => 'text',
                'default'    => 'Configuration',
                'validation' => 'max:20',
            ],
        ],
    ], [
        'key'    => 'general.settings.menu_color',
        'name'   => 'Menu Item Color Configurations',
        'info'   => 'We can change the menu items colors here.',
        'sort'   => 2,
        'fields' => [
            [
                'name'       => 'active_background_color',
                'title'      => 'Active Background Color',
                'type'       => 'color',
                'default'    => '#0E90D9',
            ], [
                'name'       => 'active_text_color',
                'title'      => 'Active Text Color',
                'type'       => 'color',
                'default'    => '#ffffff',
            ], [
                'name'       => 'text_color',
                'title'      => 'Text Color',
                'type'       => 'color',
                'default'    => '#757575',
            ],
        ],
    ],
];
