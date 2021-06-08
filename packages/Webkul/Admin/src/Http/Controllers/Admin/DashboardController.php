<?php

namespace Webkul\Admin\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Admin\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private $cards;

    private $cardData;

    private $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;

        $this->cardData = [
            [
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
            ],
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
                "card_type"     => "top_card",
                "label"         => __('admin::app.dashboard.top_leads'),
            ], [
                "selected"      => true,
                "filter_type"   => "daily",
                "card_id"       => "stages",
                "card_type"     => "stages_bar",
                "label"         => __('admin::app.dashboard.stages'),
            ], [
                "selected"      => true,
                "filter_type"   => "monthly",
                "card_id"       => "emails",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.emails'),
            ], [
                "selected"      => true,
                "filter_type"   => "monthly",
                "card_id"       => "customers",
                "card_type"     => "line_chart",
                "label"         => __('admin::app.dashboard.customers'),
            ], [
                "selected"      => true,
                "filter_type"   => "monthly",
                "card_id"       => "top_customers",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.top_customers'),
            ], [
                "selected"      => true,
                "filter_type"   => "monthly",
                "card_id"       => "products",
                "card_type"     => "line_chart",
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

        $endDate = Carbon::now()->format('Y-m-d');
        $startDate = Carbon::now()->subMonth()->addDays(1)->format('Y-m-d');

        return view('admin::dashboard.index', compact('cards', 'startDate', 'endDate'));
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
        $cardData = false;
        $requestData = request()->all();

        $cardId = $requestData['card-id'];
        $day = $requestData['filter'] ?? "today";
        $month = $requestData['filter'] ?? "this_month";

        $dateRange = $requestData['date-range'] ?? Carbon::now()->subMonth()->addDays(1)->format('Y-m-d') . "," . Carbon::now()->format('Y-m-d');
        $dateRange = explode(",", $dateRange);

        $startDateFilter = $dateRange[0];
        $endDateFilter = $dateRange[1];
        
        $startDate = Carbon::parse($startDateFilter);
        $endDate = Carbon::parse($endDateFilter);

        $totalWeeks = $startDate->diffInWeeks($endDate);

        switch ($cardId) {
            case 'leads':
                $labels = $wonLeadsCount = $lostLeadsCount = [];

                if ($totalWeeks) {
                    for ($index = $totalWeeks; $index >= 1; $index--) {
                        list(
                            'startDate' => $startDate,
                            'endDate'   => $endDate,
                            'labels'    => $labels,
                        ) = $this->getFormattedDateRange([
                            "start_date"    => $startDateFilter,
                            "end_date"      => $endDateFilter,
                            "index"         => $index,
                            "labels"        => $labels,
                            "total_weeks"   => $totalWeeks,
                        ]);
    
                        // get leads count
                        array_push($wonLeadsCount, $this->leadRepository->getLeadsCount("Won", $startDate, $endDate));
                        array_push($lostLeadsCount, $this->leadRepository->getLeadsCount("Lost", $startDate, $endDate));
                    }
                } else {
                    $labels = [__("admin::app.dashboard.week1") ];
                    
                    $wonLeadsCount = [$this->leadRepository->getLeadsCount("Won", $startDate, $endDate)];
                    $lostLeadsCount = [$this->leadRepository->getLeadsCount("Lost", $startDate, $endDate)];
                }

                if (! (empty(array_filter($wonLeadsCount)) && empty(array_filter($lostLeadsCount)))) {
                    $cardData = [
                        "data" => [
                            "labels"    => $labels,
                            "datasets"  => [
                                [
                                    "backgroundColor"   => "#4BC0C0",
                                    "data"              => $wonLeadsCount,
                                    "label"             => "Won",
                                ], [
                                    "backgroundColor"   => "#FF4D50",
                                    "data"              => $lostLeadsCount,
                                    "label"             => "Lost",
                                ]
                            ]
                        ]
                    ];
                }

                break;

            case 'products':
                $labels = $productsCount = [];
                $productRepository = app('Webkul\Product\Repositories\ProductRepository');

                if ($totalWeeks) {
                    for ($index = $totalWeeks; $index >= 1; $index--) {
                        list(
                            'startDate' => $startDate,
                            'endDate'   => $endDate,
                            'labels'    => $labels,
                        ) = $this->getFormattedDateRange([
                            "start_date"    => $startDateFilter,
                            "end_date"      => $endDateFilter,
                            "index"         => $index,
                            "labels"        => $labels,
                            "total_weeks"   => $totalWeeks,
                        ]);
    
                        // get products count
                        array_push($productsCount, $productRepository->getProductCount($startDate, $endDate));
                    }
                } else {
                    $labels = [__("admin::app.dashboard.week1") ];
                    $productsCount = [$productRepository->getProductCount($startDate, $endDate)];
                }

                if (! empty(array_filter($productsCount))) {
                    $cardData = [
                        "data" => [
                            "labels" => $labels,
                            "datasets" => [
                                [
                                    "fill"              => false,
                                    "tension"           => 0.1,
                                    "backgroundColor"   => "#4BC0C0",
                                    "label"             => "Products",
                                    "borderColor"       => 'rgb(75, 192, 192)',
                                    "data"              => $productsCount,
                                ],
                            ]
                        ]
                    ];
                }

                break;

            case 'customers':
                $labels = $customersCount = [];
                $personRepository = app('Webkul\Contact\Repositories\PersonRepository');

                if ($totalWeeks) {
                    for ($index = $totalWeeks; $index >= 1; $index--) {
                        list(
                            'startDate' => $startDate,
                            'endDate'   => $endDate,
                            'labels'    => $labels,
                        ) = $this->getFormattedDateRange([
                            "start_date"    => $startDateFilter,
                            "end_date"      => $endDateFilter,
                            "index"         => $index,
                            "labels"        => $labels,
                            "total_weeks"   => $totalWeeks,
                        ]);
    
                        // get customers count
                        array_push($customersCount, $personRepository->getCustomerCount($startDate, $endDate));
                    }
                } else {
                    $labels = [__("admin::app.dashboard.week1") ];
                    $customersCount = [$personRepository->getCustomerCount($startDate, $endDate)];
                }

                if (! empty(array_filter($customersCount))) {
                    $cardData = [
                        "data" => [
                            "labels" => $labels,
                            "datasets" => [
                                [
                                    "fill"              => false,
                                    "tension"           => 0.1,
                                    "backgroundColor"   => "#4BC0C0",
                                    "label"             => "Customers",
                                    "borderColor"       => 'rgb(75, 192, 192)',
                                    "data"              => $customersCount,
                                ],
                            ]
                        ]
                    ];
                }

                break;

            case 'activity':
                $totalCount = 0;

                $activities = app('Webkul\Lead\Repositories\ActivityRepository')
                                ->select(\DB::raw("(COUNT(*)) as count"), 'type as label')
                                ->groupBy('type')
                                ->orderBy('count', 'desc')
                                ->whereBetween('created_at', [$startDate, $endDate])
                                ->get()
                                ->toArray();

                foreach ($activities as $activity) {
                    $totalCount += $activity['count'];
                }

                $cardData = [
                    "data" => $activities,
                ];

                if ($totalCount) {
                    $cardData["header_data"] = ["$totalCount " . __("admin::app.dashboard.activities")];
                }

                break;
                
            case 'top_leads':
                $topLeads = $this->leadRepository
                            ->select('title', 'lead_value as amount', 'leads.created_at', 'status', 'lead_stages.name as statusLabel')
                            ->leftJoin('lead_stages', 'leads.lead_stage_id', '=', 'lead_stages.id')
                            ->orderBy('lead_value', 'asc')
                            ->whereBetween('leads.created_at', [$startDate, $endDate])
                            ->limit(3)
                            ->get()
                            ->toArray();

                $cardData = [
                    "data" => $topLeads
                ];
                break;

            case 'stages':
                $leadStages = [];
                $leadStagesRepository = app('Webkul\Lead\Repositories\StageRepository');

                $stages = $leadStagesRepository
                            ->select('id', 'name')
                            ->get()
                            ->toArray();

                foreach ($stages as $key => $stage) {
                    $leadsCount = $this->leadRepository
                            ->leftJoin('lead_stages', 'leads.lead_stage_id', '=', 'lead_stages.id')
                            ->where('lead_stages.id', $stage['id'])
                            ->whereBetween('leads.created_at', [$startDate, $endDate])
                            ->count();

                    switch ($stage['name']) {
                        case 'Aqcuistion':
                        case 'Propects':
                            $barType = "warning";
                            break;

                        case 'Won':
                            $barType = "success";
                            break;
                            
                        case 'Lost':
                            $barType = "danger";
                            break;

                        default:
                            $barType = "primary";
                    }
                    

                    array_push($leadStages, [
                        'label' => $stage['name'],
                        'value' => $leadsCount,
                        'bar_type' => $barType,
                    ]);
                }

                $cardData = [
                    "data" => $leadStages
                ];

                break;

            case 'emails':
                $totalEmails = $receivedEmails = $draftEmails = $outboxEmails = $sentEmails = $trashEmails = 0;
                
                $emailsCollection = app('Webkul\Email\Repositories\EmailRepository')
                                    ->whereBetween('created_at', [$startDateFilter, $endDateFilter])
                                    ->get()
                                    ->toArray();
                
                if ($emailsCollection) {
                    foreach ($emailsCollection as $key => $email) {
                        if (strpos($email->folders, 'inbox') !== false) {
                            $receivedEmails++;
                        } else if (strpos($email->folders, 'draft') !== false) {
                            $draftEmails++;
                        } else if (strpos($email->folders, 'outbox') !== false) {
                            $outboxEmails++;
                        } else if (strpos($email->folders, 'sent') !== false) {
                            $sentEmails++;
                        } else if (strpos($email->folders, 'trash') !== false) {
                            $trashEmails++;
                        }

                        $totalEmails++;
                    }
                }

                $cardData = [
                    "data" => [
                        [
                            'label' => __("admin::app.mail.total"),
                            'count' => $totalEmails
                        ], [
                            'label' => __("admin::app.mail.inbox"),
                            'count' => $receivedEmails
                        ], [
                            'label' => __("admin::app.mail.draft"),
                            'count' => $draftEmails
                        ], [
                            'label' => __("admin::app.mail.outbox"),
                            'count' => $outboxEmails
                        ], [
                            'label' => __("admin::app.mail.sent"),
                            'count' => $sentEmails
                        ], [
                            'label' => __("admin::app.mail.trash"),
                            'count' => $trashEmails
                        ],
                    ]
                ];

                break;

            case 'top_customers':
                $topCustomers = $this->leadRepository
                                ->select('persons.id as personId', 'persons.name as label', \DB::raw("(COUNT(*)) as count"))
                                ->leftJoin('persons', 'leads.person_id', '=', 'persons.id')
                                ->groupBy('person_id')
                                ->whereBetween('leads.created_at', [$startDate, $endDate])
                                ->limit(6)
                                ->orderBy('count', 'desc')
                                ->get()
                                ->toArray();

                $cardData = [
                    "data" => $topCustomers
                ];

                break;

            case 'top_products':
                $topProducts = app('Webkul\Lead\Repositories\LeadProductsRepository')
                                ->select('leads.title as label', \DB::raw("(COUNT(*)) as count"))
                                ->leftJoin('leads', 'lead_products.lead_id', '=', 'leads.id')
                                ->groupBy('product_id')
                                ->whereBetween('lead_products.created_at', [$startDate, $endDate])
                                ->get()
                                ->toArray();

                $cardData = [
                    "data" => $topProducts
                ];
                break;

            default:
                $cardData = [
                    "data" => []
                ];
        }

        return response()->json($cardData);
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

    private function getFormattedDateRange($data)
    {
        $labels = $data['labels'];

        $startDate = Carbon::parse($data["start_date"]);
        $endDate = Carbon::parse($data["end_date"]);

        array_push($labels, __("admin::app.dashboard.week" . (($data['total_weeks'] + 1) - $data['index'])));
        
        $startDate = $data['index'] != $data['total_weeks']
                    ? $startDate->addDays((7 * (4 - $data['index'])) + (4 - $data['index']))
                    : $startDate->addDays(7 * (4 - $data['index']));

        $endDate = $data['index'] == 1 ? $endDate->addDays(1) : (clone $startDate)->addDays(7);
        
        $startDate = $startDate->format('Y-m-d');
        $endDate = $endDate->format('Y-m-d');

        return compact('startDate', 'endDate', 'labels');
    }
}