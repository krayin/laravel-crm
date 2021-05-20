<?php

namespace Webkul\Admin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $leadData = [
            "filter_type"   => "monthly",
            "card_type"     => "bar_chart",
            "labels"        => [
                "Week1",
                "Week2",
                "Week3",
                "Week4",
                "Week5"
            ],
            "datasets"      => [[
                "backgroundColor"   => "#4BC0C0",
                "data"              => [0, 2, 5, 5, 9],
                "label"             => "Number of Moons",
            ], [
                "backgroundColor"   => "#FF4D50",
                "data"              => [0, 2, 5, 5, 9],
                "label"             => "Planetary Mass (relative to the Sun x 10^-6)",
            ]]
        ];
    
        $activityData = [
            "total"         => 10,
            "filter_type"   => "daily",
            "card_type"     => "activity",
            "header_data"   => ["10 Activities", "2 New Leads"],
            "data"          => [
                [
                    'label' => 'Phone call',
                    'value' => 2,
                ], [
                    'label' => 'Email',
                    'value' => 7,
                ], [
                    'label' => 'Meeting',
                    'value' => 1,
                ],
            ]
        ];
    
        $dealData = [
            "filter_type"   => "daily",
            "card_type"     => "top_deals",
            "data"          => [
                [
                    'label'         => 'Plan to attend a Training',
                    'amount'        => 50000.00,
                    'created_at'    => '23 Mar 2021 20:08',
                    'status'        => 1,
                    'statusLabel'   => 'Aqcuisition',
                ], [
                    'label'         => 'Information about laptop',
                    'amount'        => 50000.00,
                    'created_at'    => '23 Mar 2021 20:08',
                    'status'        => 2,
                    'statusLabel'   => 'Prospect',
                ], [
                    'label'         => 'Manage my website',
                    'amount'        => 50000.00,
                    'created_at'    => '23 Mar 2021 20:08',
                    'status'        => 1,
                    'statusLabel'   => 'Aqcuisition',
                ],
            ]
        ];
    
        $leadStages = [
            "total"         => 10,
            "filter_type"   => "daily",
            "card_type"     => "stages_bar",
            "data"          => [
                [
                    'label'     => 'New',
                    'value'     => 2,
                    'bar_type'  => 'primary',
                ], [
                    'label'     => 'Aqcuistion',
                    'value'     => 7,
                    'bar_type'  => 'warning',
                ], [
                    'label'     => 'Propects',
                    'value'     => 1,
                    'bar_type'  => 'warning',
                ], [
                    'label'     => 'Won',
                    'value'     => 1,
                    'bar_type'  => 'success',
                ], [
                    'label'     => 'Lost',
                    'value'     => 3,
                    'bar_type'  => 'danger',
                ],
            ]
        ];
    
        $emailData = [
            "filter_type"   => "monthly",
            "card_type"     => "emails",
            "data"          => [
                [
                    'label' => 'New Contact Mails',
                    'count' => 5
                ], [
                    'label' => 'Total Email Received',
                    'count' => 2548
                ], [
                    'label' => 'Total Mail Sent',
                    'count' => 2548
                ], [
                    'label' => 'Total Email Received',
                    'count' => 2548
                ], [
                    'label' => 'Total Email Response',
                    'count' => 25
                ], [
                    'label' => 'Email Response',
                    'count' => "20.3%"
                ],
            ]
        ];


        $cards = [
            "card_type" => "custom_card",
        ];

        return view('admin::dashboard.index', compact('leadData', 'activityData', 'dealData', 'leadStages', 'emailData'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function template()
    {
        return view('admin::dashboard.template');
    }
}