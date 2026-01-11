<?php

return [
    [
        'key' => 'lawfirm',
        'name' => 'lawfirm::app.acl.lawfirm', // Verifique se há tradução ou use string direta
        'route' => 'admin.processos.index',
        'sort' => 1,
    ],
    [
        'key' => 'lawfirm.processos',
        'name' => 'lawfirm::app.acl.processos',
        'route' => 'admin.processos.index',
        'sort' => 1,
    ],
    [
        'key' => 'lawfirm.financial',
        'name' => 'lawfirm::app.acl.financial',
        'route' => 'admin.processos.index', // Ou rota específica financeira se houver
        'sort' => 2,
    ],
    [
        'key' => 'lawfirm.prazos',
        'name' => 'lawfirm::app.acl.prazos',
        'route' => 'admin.prazos.index',
        'sort' => 3,
    ],
];
