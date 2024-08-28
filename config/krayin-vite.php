<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Krayin Vite Configuration
    |--------------------------------------------------------------------------
    |
    | Please add your Vite registry here to seamlessly support the `assets` function.
    |
    */

    'viters' => [
        'admin' => [
            'hot_file'                 => 'admin-vite.hot',
            'build_directory'          => 'admin/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],

        'installer' => [
            'hot_file'                 => 'installer-vite.hot',
            'build_directory'          => 'installer/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],

        'webform' => [
            'hot_file'                 => 'webform-vite.hot',
            'build_directory'          => 'webform/build',
            'package_assets_directory' => 'src/Resources/assets',
        ],
    ],
];
