<?php

return [
    'importers' => [
        'persons' => [
            'title' => 'Pessoas',

            'validation' => [
                'errors' => [
                    'duplicate-email' => 'E-mail : \'%s\' é encontrado mais de uma vez no arquivo de importação.',
                    'duplicate-phone' => 'Telefone : \'%s\' é encontrado mais de uma vez no arquivo de importação.',
                    'email-not-found' => 'E-mail : \'%s\' não foi encontrado no sistema.',
                ],
            ],
        ],

        'products' => [
            'title' => 'Produtos',

            'validation' => [
                'errors' => [
                    'sku-not-found' => 'Produto com este código não foi encontrado',
                ],
            ],
        ],

        'leads' => [
            'title' => 'Oportunidades',

            'validation' => [
                'errors' => [
                    'id-not-found' => 'ID : \'%s\' não foi encontrado no sistema.',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => 'As colunas de número "%s" têm cabeçalhos vazios.',
            'column-name-invalid'  => 'Nomes de colunas inválidos: "%s".',
            'column-not-found'     => 'Colunas obrigatórias não encontradas: %s.',
            'column-numbers'       => 'O número de colunas não corresponde ao número de linhas no cabeçalho.',
            'invalid-attribute'    => 'O cabeçalho contém atributo(s) inválido(s): "%s".',
            'system'               => 'Ocorreu um erro inesperado no sistema.',
            'wrong-quotes'         => 'Aspas curvas usadas em vez de aspas retas.',
            'already-exists'       => 'O :attribute já existe.',
        ],
    ],
];
