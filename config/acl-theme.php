<?php

/**
 * ACL configuration para Theme Manager (upgrade-safe).
 *
 * Este arquivo é carregado pelo ThemeBootProvider e mesclado com as ACLs
 * do sistema durante o boot. Segue o mesmo padrão do Krayin core.
 *
 * Estrutura:
 * - key: Identificador único da permissão (usado em bouncer()->hasPermission())
 * - name: Chave de tradução para o nome da permissão
 * - route: Rota(s) associada(s) à permissão
 * - sort: Ordem de exibição na árvore de permissões
 */

return [
    // Permissão principal para gerenciamento de temas
    [
        'key'   => 'settings.theme',
        'name'  => 'Gerenciamento de Temas',
        'route' => 'admin.settings.theme.index',
        'sort'  => 5, // Após other_settings (4) na árvore de settings
    ],

    // Visualizar configurações de tema
    [
        'key'   => 'settings.theme.view',
        'name'  => 'Visualizar',
        'route' => 'admin.settings.theme.index',
        'sort'  => 1,
    ],

    // Editar/aplicar temas
    [
        'key'   => 'settings.theme.edit',
        'name'  => 'Editar',
        'route' => [
            'admin.settings.theme.update',
            'admin.settings.theme.preview',
        ],
        'sort'  => 2,
    ],

    // Restaurar tema padrão / rollback
    [
        'key'   => 'settings.theme.restore',
        'name'  => 'Restaurar',
        'route' => [
            'admin.settings.theme.restore',
            'admin.settings.theme.rollback',
        ],
        'sort'  => 3,
    ],
];
