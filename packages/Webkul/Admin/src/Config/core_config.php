<?php

return [
    /**
     * General.
     */
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
        'key'  => 'general.magic_ai',
        'name' => 'admin::app.configuration.index.magic-ai.title',
        'info' => 'admin::app.configuration.index.magic-ai.info',
        'icon' => 'icon-setting',
        'sort' => 3,
    ], [
        'key'    => 'general.magic_ai.settings',
        'name'   => 'admin::app.configuration.index.magic-ai.settings.title',
        'info'   => 'admin::app.configuration.index.magic-ai.settings.info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'enable',
                'title'         => 'admin::app.configuration.index.magic-ai.settings.enable',
                'type'          => 'boolean',
                'channel_based' => true,
            ], [
                'name'          => 'model',
                'title'         => 'admin::app.configuration.index.magic-ai.settings.models.title',
                'type'          => 'select',
                'channel_based' => true,
                'depends'       => 'enable:1',
                'options'       => [
                    [
                        'title' => 'admin::app.configuration.index.magic-ai.settings.models.gpt-4o',
                        'value' => 'gpt-4o',
                    ], [
                        'title' => 'admin::app.configuration.index.magic-ai.settings.models.gpt-4o-mini',
                        'value' => 'gpt-4o-mini',
                    ], [
                        'title' => 'admin::app.configuration.index.magic-ai.settings.models.gemini-flash',
                        'value' => 'gemini-1.5-flash',
                    ], [
                        'title' => 'admin::app.configuration.index.magic-ai.settings.models.deepseek-r1',
                        'value' => 'deepseek-r1:8b',
                    ], [
                        'title' => 'admin::app.configuration.index.magic-ai.settings.models.ollama',
                        'value' => 'llama3.2:latest',
                    ], [
                        'title' => 'admin::app.configuration.index.magic-ai.settings.models.llama',
                        'value' => 'llama-3.3-70b-versatile',
                    ],
                ],
            ], [
                'name'          => 'api_key',
                'title'         => 'admin::app.configuration.index.magic-ai.settings.api-key',
                'type'          => 'password',
                'depends'       => 'enable:1,model:gpt-4o,model:gpt-4o-mini,model:gemini-1.5-flash,model:llama-3.3-70b-versatile',
                'validation'    => 'required_if:enable,1',
                'info'          => 'admin::app.configuration.index.magic-ai.settings.api-key-info',
            ], [
                'name'          => 'api_domain',
                'title'         => 'admin::app.configuration.index.magic-ai.settings.api-domain',
                'type'          => 'text',
                'info'          => 'admin::app.configuration.index.magic-ai.settings.api-domain-info',
                'depends'       => 'enable:1',
            ],
        ],
    ], [
        'key'    => 'general.magic_ai.pdf_generation',
        'name'   => 'admin::app.configuration.index.magic-ai.settings.pdf-generation',
        'info'   => 'admin::app.configuration.index.magic-ai.settings.pdf-generation-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'enabled',
                'title'         => 'admin::app.configuration.index.magic-ai.settings.enable',
                'type'          => 'boolean',
            ],
        ],
    ],

    /**
     * Email.
     */
    [
        'key'  => 'email',
        'name' => 'admin::app.configuration.index.email.title',
        'info' => 'admin::app.configuration.index.email.info',
        'sort' => 2,
    ], [
        'key'  => 'email.imap',
        'name' => 'admin::app.configuration.index.email.imap.title',
        'info' => 'admin::app.configuration.index.email.imap.info',
        'icon' => 'icon-setting',
        'sort' => 1,
    ], [
        'key'    => 'email.imap.account',
        'name'   => 'admin::app.configuration.index.email.imap.account.title',
        'info'   => 'admin::app.configuration.index.email.imap.account.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'host',
                'title'   => 'admin::app.configuration.index.email.imap.account.host',
                'type'    => 'text',
                'default' => config('imap.accounts.default.host'),
            ],
            [
                'name'    => 'port',
                'title'   => 'admin::app.configuration.index.email.imap.account.port',
                'type'    => 'text',
                'default' => config('imap.accounts.default.port'),
            ],
            [
                'name'    => 'encryption',
                'title'   => 'admin::app.configuration.index.email.imap.account.encryption',
                'type'    => 'text',
                'default' => config('imap.accounts.default.encryption'),
            ],
            [
                'name'    => 'validate_cert',
                'title'   => 'admin::app.configuration.index.email.imap.account.validate-cert',
                'type'    => 'boolean',
                'default' => config('imap.accounts.default.validate_cert'),
            ],
            [
                'name'    => 'username',
                'title'   => 'admin::app.configuration.index.email.imap.account.username',
                'type'    => 'text',
                'default' => config('imap.accounts.default.username'),
            ],
            [
                'name'    => 'password',
                'title'   => 'admin::app.configuration.index.email.imap.account.password',
                'type'    => 'password',
                'default' => config('imap.accounts.default.password'),
            ],
        ],
    ],
];
