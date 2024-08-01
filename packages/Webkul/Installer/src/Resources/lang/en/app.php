<?php

return [
    'seeders' => [
        'attributes' => [
            'leads' => [
                'description'         => 'Description',
                'expected-close-date' => 'Expected Close Date',
                'lead-value'          => 'Lead Value',
                'sales-owner'         => 'Sales Owner',
                'source'              => 'Source',
                'title'               => 'Title',
                'type'                => 'Type',
            ],

            'persons' => [
                'contact-numbers' => 'Contact Numbers',
                'emails'          => 'Emails',
                'job-title'       => 'Job Title',
                'name'            => 'Name',
                'organization'    => 'Organization',
            ],

            'organizations' => [
                'address' => 'Address',
                'name'    => 'Name',
            ],

            'products' => [
                'description' => 'Description',
                'name'        => 'Name',
                'price'       => 'Price',
                'quantity'    => 'Quantity',
                'sku'         => 'SKU',
            ],

            'quotes' => [
                'adjustment-amount' => 'Adjustment Amount',
                'billing-address'   => 'Billing Address',
                'description'       => 'Description',
                'discount-amount'   => 'Discount Amount',
                'discount-percent'  => 'Discount Percent',
                'expired-at'        => 'Expired At',
                'grand-total'       => 'Grand Total',
                'person'            => 'Person',
                'sales-owner'       => 'Sales Owner',
                'shipping-address'  => 'Shipping Address',
                'sub-total'         => 'Sub Total',
                'subject'           => 'Subject',
                'tax-amount'        => 'Tax Amount',
            ],

            'warehouses' => [
                'contact-address' => 'Contact Address',
                'contact-emails'  => 'Contact Emails',
                'contact-name'    => 'Contact Name',
                'contact-numbers' => 'Contact Numbers',
                'description'     => 'Description',
                'name'            => 'Name',
            ],
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
                'direct'   => 'Direct',
                'email'    => 'Email',
                'phone'    => 'Phone',
                'web'      => 'Web',
                'web-form' => 'Web Form',
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
    ],
];
