<?php

return [
    'importers' => [
        'persons' => [
            'title' => 'Personas',

            'validation' => [
                'errors' => [
                    'duplicate-email' => 'Correo electrónico: \'%s\' se encontró más de una vez en el archivo de importación.',
                    'duplicate-phone' => 'Teléfono: \'%s\' se encontró más de una vez en el archivo de importación.',
                    'email-not-found' => 'Correo electrónico: \'%s\' no se encontró en el sistema.',
                ],
            ],
        ],

        'products' => [
            'title' => 'Productos',

            'validation' => [
                'errors' => [
                    'sku-not-found' => 'Producto con el SKU especificado no encontrado.',
                ],
            ],
        ],

        'leads' => [
            'title' => 'Clientes Potenciales',

            'validation' => [
                'errors' => [
                    'id-not-found' => 'ID: \'%s\' no se encuentra en el sistema.',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => 'Las columnas número "%s" tienen encabezados vacíos.',
            'column-name-invalid'  => 'Nombres de columnas no válidos: "%s".',
            'column-not-found'     => 'No se encontraron las columnas requeridas: %s.',
            'column-numbers'       => 'El número de columnas no corresponde al número de filas en el encabezado.',
            'invalid-attribute'    => 'El encabezado contiene atributos no válidos: "%s".',
            'system'               => 'Ocurrió un error inesperado en el sistema.',
            'wrong-quotes'         => 'Se usaron comillas curvas en lugar de comillas rectas.',
        ],
    ],
];
