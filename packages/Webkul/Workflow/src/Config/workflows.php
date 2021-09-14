<?php

return [
    'trigger_entities' => [
        
        'persons' => [
            'name'   => 'Persons',
            'class'  => 'Webkul\Workflow\Helpers\Entity\Person',
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
            ]
        ],

        'leads' => [
            'name'   => 'Leads',
            'class'  => 'Webkul\Workflow\Helpers\Entity\Lead',
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
            ]
        ],

        'quotes' => [
            'name'   => 'Quotes',
            'class'  => 'Webkul\Workflow\Helpers\Entity\Quote',
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
            ]
        ]
    ]
];