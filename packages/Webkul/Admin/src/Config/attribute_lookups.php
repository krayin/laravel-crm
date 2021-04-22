<?php

return [
    'organizations' => [
        'name'         => 'Organization',
        'value_column' => 'id',
        'label_column' => 'name',
        'repository'   => 'Webkul\Contact\Repositories\OrganizationRepository',
    ],

    'lead_sources' => [
        'name'         => 'Source',
        'value_column' => 'id',
        'label_column' => 'name',
        'repository'   => 'Webkul\Lead\Repositories\SourceRepository',
    ],

    'lead_types' => [
        'name'         => 'Type',
        'value_column' => 'id',
        'label_column' => 'name',
        'repository'   => 'Webkul\Lead\Repositories\TypeRepository',
    ],
];