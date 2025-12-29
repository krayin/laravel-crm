<?php

return [
    'name' => 'ThemeManager',

    'version' => '1.0.0',

    'defaults' => [
        'colors' => [
            'primary'       => '#1E40AF',
            'primary_dark'  => '#1E3A8A',
            'primary_light' => '#3B82F6',
            'success'       => '#10B981',
            'warning'       => '#F59E0B',
            'danger'        => '#EF4444',
        ],

        'login' => [
            'bg_zoom'            => 100,
            'bg_opacity'         => 50,
            'show_powered_by'    => true,
            'card_enabled'       => false,
            'card_bg_opacity'    => 62,
            'card_overlay_color' => 'rgba(10, 45, 15, 0.78)',
            'card_title'         => 'Bem-vindo',
            'card_subtitle'      => 'Acesse sua conta para continuar',
            'card_sparkles'      => false,
            'card_help_link'     => true,
            'card_support_email' => 'suporte@empresa.com.br',
        ],
    ],
];
