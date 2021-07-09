<?php

return [
    'organizations' => [
        'name'         => 'Organization',
        'repository'   => 'Webkul\Contact\Repositories\OrganizationRepository',
    ],

    'lead_sources' => [
        'name'         => 'Lead Source',
        'repository'   => 'Webkul\Lead\Repositories\SourceRepository',
    ],

    'lead_types' => [
        'name'         => 'Lead Type',
        'repository'   => 'Webkul\Lead\Repositories\TypeRepository',
    ],

    'lead_pipelines' => [
        'name'         => 'Lead Pipelines',
        'repository'   => 'Webkul\Lead\Repositories\PipelineRepository',
    ],

    'lead_stages' => [
        'name'         => 'Lead Stage',
        'repository'   => 'Webkul\Lead\Repositories\StageRepository',
    ],

    'users' => [
        'name'         => 'Sales Owner',
        'repository'   => 'Webkul\User\Repositories\UserRepository',
    ],

    'persons' => [
        'name'         => 'Persons',
        'repository'   => 'Webkul\Contact\Repositories\PersonRepository',
    ]
];