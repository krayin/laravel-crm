<?php

namespace Webkul\Admin\Http\Controllers\Admin;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private $cards;

    private $cardData;

    public function __construct()
    {
        $this->cardData = [
            [
                "card_id"       => "leads",
                "data"          => [
                    "labels" => [
                        "Week1",
                        "Week2",
                        "Week3",
                        "Week4",
                        "Week5"
                    ],
                    "datasets" => [
                        [
                            "backgroundColor"   => "#4BC0C0",
                            "data"              => [0, 2, 5, 5, 9],
                            "label"             => "Won Leads",
                        ], [
                            "backgroundColor"   => "#FF4D50",
                            "data"              => [0, 2, 5, 5, 9],
                            "label"             => "Lost Leads",
                        ]
                    ]
                ]
            ], [
                "total"         => 10,
                "card_id"       => "activity",
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
            ], [
                "card_id"       => "top_leads",
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
            ], [
                "total"         => 10,
                "card_id"       => "stages_bar",
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
            ], [
                "card_id"       => "emails",
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
            ], [
                "card_id"       => "customers",
                "data"          => [
                    [
                        'label' => 'New Contacts',
                        'count' => 5
                    ], [
                        'label' => 'Total Contacts',
                        'count' => 500
                    ], [
                        'label' => 'New Companies',
                        'count' => 1
                    ], [
                        'label' => 'Total Companies',
                        'count' => 25
                    ], [
                        'label' => 'Contact convert to Won',
                        'count' => "2%"
                    ], [
                        'label' => 'Won companies',
                        'count' => "0.23%"
                    ],
                ]
            ]
        ];

        $this->cards = [
            [
                "selected"      => true,
                "card_id"       => "leads",
                "filter_type"   => "monthly",
                "card_type"     => "bar_chart",
                "label"         => __('admin::app.leads.title'),
            ], [
                "selected"      => true,
                "filter_type"   => "daily",
                "card_id"       => "activity",
                "card_type"     => "activity",
                "label"         => __('admin::app.dashboard.activity'),
            ], [
                "selected"      => true,
                "filter_type"   => "daily",
                "card_id"       => "top_leads",
                "card_type"     => "top_leads",
                "label"         => __('admin::app.dashboard.top_leads'),
            ], [
                "selected"      => true,
                "filter_type"   => "daily",
                "card_id"       => "stages_bar",
                "card_type"     => "stages_bar",
                "label"         => __('admin::app.dashboard.stages'),
            ], [
                "selected"      => true,
                "filter_type"   => "monthly",
                "card_id"       => "emails",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.emails'),
            ], [
                "selected"      => false,
                "filter_type"   => "monthly",
                "card_id"       => "customers",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.customers'),
            ], [
                "selected"      => false,
                "filter_type"   => "monthly",
                "card_id"       => "top_customers",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.top_customers'),
            ], [
                "selected"      => false,
                "filter_type"   => "monthly",
                "card_id"       => "products",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.products'),
            ], [
                "selected"      => true,
                "filter_type"   => "monthly",
                "card_id"       => "top_products",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.top_products'),
            ], [
                "card_type"     => "custom_card",
                "card_border"   => "dashed",
                "selected"      => false,
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cards = $this->cards;

        return view('admin::dashboard.index', compact('cards'));
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

    /**
     * Returns json data for dashboard card.
     */
    public function getCardData()
    {
        $response = [];
        $requestData = request()->all();

        $cardId = $requestData['card-id'];

        foreach ($this->cardData as $card) {
            if ($card['card_id'] == $cardId) {
                $response = $card;
            }
        }

        return response()->json($response);
    }

    /**
     * Returns json data for available dashboard cards.
     */
    public function getCards()
    {
        $response = $this->cards;

        return response()->json($response);
    }

    /**
     * Returns updated json data for available dashboard cards.
     */
    public function updateCards()
    {
        $requestData = request()->all();

        foreach ($requestData['cards'] as $requestedCardData) {
            foreach ($this->cards as $cardIndex => $card) {
                if (isset($card['card_id']) && isset($requestedCardData['card_id']) && $card['card_id'] == $requestedCardData['card_id']) {
                    $this->cards[$cardIndex]['selected'] = $requestedCardData['selected'];
                }
            }
        }

        return response()->json($this->cards);
    }
}