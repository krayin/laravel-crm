<?php

return [
    [
        'key' => 'lawfirm',
        'name' => 'Jurídico',
        'route' => 'admin.processos.index',
        'sort' => 2,
        'icon-class' => 'icon-note',
        'permission' => 'lawfirm',
    ],
    [
        'key' => 'lawfirm.processos',
        'name' => 'Processos',
        'route' => 'admin.processos.index',
        'sort' => 1,
        'icon-class' => '',
        'permission' => 'lawfirm.processos',
    ],
    [
        'key' => 'lawfirm.prazos',
        'name' => 'Prazos',
        'route' => 'admin.prazos.index',
        'sort' => 2,
        'icon-class' => 'icon-calendar',
        'permission' => 'lawfirm.prazos',
    ],
    [
        'key' => 'lawfirm.financial',
        'name' => 'Dashboard Financeiro',
        'route' => 'admin.lawfirm.financial.index',
        'sort' => 3,
        'icon-class' => 'icon-dashboard',
        'permission' => 'lawfirm.financial',
    ],
];
