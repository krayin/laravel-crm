<?php

return [
    'trigger_entities' => [

        'leads' => [
            'name'   => 'Leads',
            'class'  => 'Webkul\Automation\Helpers\Entity\Lead',
            'events' => [
                [
                    'event' => 'lead.create.after',
                    'name'  => 'Created',
                ], [
                    'event' => 'lead.update.after',
                    'name'  => 'Updated',
                ], [
                    'event' => 'lead.delete.before',
                    'name'  => 'Deleted',
                ],
            ],
        ],

        'activities' => [
            'name'   => 'Activities',
            'class'  => 'Webkul\Automation\Helpers\Entity\Activity',
            'events' => [
                [
                    'event' => 'activity.create.after',
                    'name'  => 'Created',
                ], [
                    'event' => 'activity.update.after',
                    'name'  => 'Updated',
                ], [
                    'event' => 'activity.delete.before',
                    'name'  => 'Deleted',
                ],
            ],
        ],

        'persons' => [
            'name'   => 'Persons',
            'class'  => 'Webkul\Automation\Helpers\Entity\Person',
            'events' => [
                [
                    'event' => 'contacts.person.create.after',
                    'name'  => 'Created',
                ], [
                    'event' => 'contacts.person.update.after',
                    'name'  => 'Updated',
                ], [
                    'event' => 'contacts.person.delete.before',
                    'name'  => 'Deleted',
                ],
            ],
        ],

        'quotes' => [
            'name'   => 'Quotes',
            'class'  => 'Webkul\Automation\Helpers\Entity\Quote',
            'events' => [
                [
                    'event' => 'quote.create.after',
                    'name'  => 'Created',
                ], [
                    'event' => 'quote.update.after',
                    'name'  => 'Updated',
                ], [
                    'event' => 'quote.delete.before',
                    'name'  => 'Deleted',
                ],
            ],
        ],
    ],
];
