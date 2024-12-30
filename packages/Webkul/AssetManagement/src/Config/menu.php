<?php

return [
    [
        'key'        => 'assetmanagement',
        'name'       => 'Asset Management',
        'route'      => 'admin.assetmanagement.index',
        'sort'       => 11,
        'icon-class' => 'icon-assetManagement',
    ],
    [
        'key'        => 'assetmanagement.instrument',
        'name'       => 'Instrument List',
        'route'      => 'admin.instrument.index',
        'sort'       => 1,
        'icon-class' => '',
    ],
    [
        'key'        => 'assetmanagement.warehouse',
        'name'       => 'Warehouse Asset List',
        'route'      => 'admin.warehouse.index',
        'sort'       => 2,
        'icon-class' => '',
    ],
    [
        'key'        => 'assetmanagement.utilization',
        'name'       => 'Asset Utilization Chart',
        'route'      => 'admin.assetUtilization.index',
        'sort'       => 3,
        'icon-class' => '',
    ],

];
