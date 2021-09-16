<?php

return [
    "activities" => [
        [
            'type'      => 'pill',
            'key'       => 'type',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'      => 'admin::app.leads.all',
                    'isActive'  => true,
                    'key'       => 'all',
                ], [
                    'name'      => 'admin::app.leads.call',
                    'isActive'  => false,
                    'key'       => 'call',
                ], [
                    'name'      => 'admin::app.leads.meeting',
                    'isActive'  => false,
                    'key'       => 'meeting',
                ], [
                    'name'      => 'admin::app.leads.lunch',
                    'isActive'  => false,
                    'key'       => 'lunch',
                ]
            ]
        ], [
            'type'      => 'group',
            'key'       => 'scheduled',
            'condition' => 'eq',
            'values'    => [
                [
                    'name'      => 'admin::app.datagrid.filters.yesterday',
                    'isActive'  => false,
                    'key'       => 'yesterday',
                ], [
                    'name'      => 'admin::app.datagrid.filters.today',
                    'isActive'  => false,
                    'key'       => 'today',
                ], [
                    'name'      => 'admin::app.datagrid.filters.tomorrow',
                    'isActive'  => false,
                    'key'       => 'tomorrow',
                ], [
                    'name'      => 'admin::app.datagrid.filters.this-week',
                    'isActive'  => false,
                    'key'       => 'this_week',
                ], [
                    'name'      => 'admin::app.datagrid.filters.this-month',
                    'isActive'  => true,
                    'key'       => 'this_month',
                ], [
                    'name'      => 'admin::app.datagrid.filters.custom',
                    'isActive'  => false,
                    'key'       => 'custom',
                ]
            ]
        ],
    ],

    "leads" => [
        [
            'type'              => 'pill',
            'key'               => 'type',
            'condition'         => 'eq',
            "value_type"        => "lookup",
            "repositoryClass"   => "\Webkul\Lead\Repositories\StageRepository",
        ]
    ]
];