<?php

return [
    'seeders' => [
        'attributes' => [
            'address'             => 'Address',
            'adjustment-amount'   => 'Adjustment Amount',
            'billing-address'     => 'Billing Address',
            'contact-numbers'     => 'Contact Numbers',
            'description'         => 'Description',
            'discount-amount'     => 'Discount Amount',
            'discount-percent'    => 'Discount Percent',
            'emails'              => 'Emails',
            'expected-close-date' => 'Expected Close Date',
            'expired-at'          => 'Expired At',
            'grand-total'         => 'Grand Total',
            'lead-value'          => 'Lead Value',
            'name'                => 'Name',
            'organization'        => 'Organization',
            'person'              => 'Person',
            'price'               => 'Price',
            'quantity'            => 'Quantity',
            'sales-owner'         => 'Sales Owner',
            'shipping-address'    => 'Shipping Address',
            'sku'                 => 'SKU',
            'source'              => 'Source',
            'sub-total'           => 'Sub Total',
            'subject'             => 'Subject',
            'tax-amount'          => 'Tax Amount',
            'title'               => 'Title',
            'type'                => 'Type',
        ],

        'email' => [
            'activity-created'      => 'Activity created',
            'activity-modified'     => 'Activity modified',
            'date'                  => 'Date',
            'new-activity'          => 'You have a new activity, please find the details bellow',
            'new-activity-modified' => 'You have a new activity modified, please find the details bellow',
            'participants'          => 'Participants',
            'title'                 => 'Title',
            'type'                  => 'Type',
        ],
        
        'lead' => [
            'pipeline' => [
                'default' => 'Default Pipeline',

                'pipeline-stages' => [
                    'follow-up'   => 'Follow Up',
                    'lost'        => 'Lost',
                    'negotiation' => 'Negotiation',
                    'new'         => 'New',
                    'prospect'    => 'Prospect',
                    'won'         => 'Won',
                ],
            ],

            'source' => [
                'direct'    => 'Direct',
                'email'     => 'Email',
                'phone'     => 'Phone',
                'web'       => 'Web',
                'web-form'  => 'Web Form',
            ],

            'type' => [
                'existing-business' => 'Existing Business',
                'new-business'      => 'New Business',
            ],
        ],

        'user' => [
            'role' => [
                'administrator-role' => 'Administrator Role',
                'administrator'      => 'Administrator',
            ],
        ],

        'workflow' => [
            'email-to-participants-after-activity-updation' => 'Emails to participants after activity updation',
            'email-to-participants-after-activity-creation' => 'Emails to participants after activity creation',
        ],

        'warehouses' => [
            'contact-name'    => 'Contact Name',
            'contact-emails'  => 'Contact Emails',
            'contact-numbers' => 'Contact Numbers',
            'contact-address' => 'Contact Address',
            'description'     => 'Description',
            'name'            => 'Name',
        ],
    ],
];
