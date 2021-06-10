<?php

namespace Webkul\Admin\Traits;

use Carbon\Carbon;

trait DashboardHelper
{
    /**
     * this will set all available cards data to be displayed on dashboard.
     */
    private function setCardsData()
    {
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
     * This will return date range to be applied on dashboard data.
     */
    private function getDateRangeDetails($data)
    {
        $cardId = $data['card-id'];

        $dateRange = $data['date-range'] ?? Carbon::now()->subMonth()->addDays(1)->format('Y-m-d') . "," . Carbon::now()->format('Y-m-d');
        $dateRange = explode(",", $dateRange);

        $startDateFilter = $dateRange[0];
        $endDateFilter = $dateRange[1];
        
        $startDate = Carbon::parse($startDateFilter);
        $endDate = Carbon::parse($endDateFilter);

        $totalWeeks = $startDate->diffInWeeks($endDate);

        return compact(
            'cardId',
            'startDate',
            'endDate',
            'totalWeeks',
            'startDateFilter',
            'endDateFilter'
        );
    }

    /**
     * format dates of filter.
     */
    private function getFormattedDateRange($data)
    {
        $labels = $data['labels'];

        $startDate = Carbon::parse($data["start_date"]);
        $endDate = Carbon::parse($data["end_date"]);

        array_push($labels, __("admin::app.dashboard.week") . (($data['total_weeks'] + 1) - $data['index']));
        
        $startDate = $data['index'] != $data['total_weeks']
                    ? $startDate->addDays((7 * (4 - $data['index'])) + (4 - $data['index']))
                    : $startDate->addDays(7 * (4 - $data['index']));

        $endDate = $data['index'] == 1 ? $endDate->addDays(1) : (clone $startDate)->addDays(7);
        
        $startDate = $startDate->format('Y-m-d');
        $endDate = $endDate->format('Y-m-d');

        return compact('startDate', 'endDate', 'labels');
    }

    /**
     * Collect card data based on cardId.
     */
    private function getFormattedCardData($requestData)
    {
        $relevantFunction = false;

        list(
            'cardId'            => $cardId,
            'endDate'           => $endDate,
            'startDate'         => $startDate,
            'totalWeeks'        => $totalWeeks,
            'endDateFilter'     => $endDateFilter,
            'startDateFilter'   => $startDateFilter,
        ) = $this->getDateRangeDetails($requestData);

        switch ($cardId) {
            case 'leads':
                $relevantFunction = "getLeads";

                break;
            case 'products':
                $relevantFunction = "getProducts";

                break;
            case 'customers':
                $relevantFunction = "getCustomers";

                break;
            case 'activity':
                $relevantFunction = "getActivities";

                break;
            case 'top_leads':
                $relevantFunction = "getTopLeads";

                break;
            case 'stages':
                $relevantFunction = "getStages";

                break;
            case 'emails':
                $relevantFunction = "getEmails";
                
                break;
            case 'top_customers':
                $relevantFunction = "getTopCustomers";
                
                break;
            case 'top_products':
                $relevantFunction = "getTopProducts";
                
                break;
            default:
                $cardData = [
                    "data" => []
                ];
        }

        $cardData = $relevantFunction
                    ? $this->{$relevantFunction}(
                        $startDateFilter,
                        $endDateFilter,
                        $totalWeeks
                    )
                    : $cardData ?? false;

        return $cardData;
    }

    /**
     * Collect leads card data.
     */
    private function getLeads($startDateFilter, $endDateFilter, $totalWeeks)
    {
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
            $labels = [__("admin::app.dashboard.week") . "1"];
            
            $wonLeadsCount = [$this->leadRepository->getLeadsCount("Won", $startDateFilter, $endDateFilter)];
            $lostLeadsCount = [$this->leadRepository->getLeadsCount("Lost", $startDateFilter, $endDateFilter)];
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

        return $cardData ?? false;
    }

    /**
     * Collect Products card data.
     */
    private function getProducts($startDateFilter, $endDateFilter, $totalWeeks)
    {
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
            $labels = [__("admin::app.dashboard.week") . "1"];
            $productsCount = [$productRepository->getProductCount($startDateFilter, $endDateFilter)];
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
        
        return $cardData ?? false;
    }

    /**
     * Collect Customers card data.
     */
    private function getCustomers($startDateFilter, $endDateFilter, $totalWeeks)
    {
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
            $labels = [__("admin::app.dashboard.week") . "1"];
            $customersCount = [$personRepository->getCustomerCount($startDateFilter, $endDateFilter)];
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

        return $cardData ?? false;
    }

    /**
     * Collect Activity card data.
     */
    private function getActivities($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $totalCount = 0;

        $activities = app('Webkul\Lead\Repositories\ActivityRepository')
                        ->select(\DB::raw("(COUNT(*)) as count"), 'type as label')
                        ->groupBy('type')
                        ->orderBy('count', 'desc')
                        ->whereBetween('created_at', [$startDateFilter, $endDateFilter])
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

        return $cardData;
    }

    /**
     * Collect TopLeads card data.
     */
    private function getTopLeads($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $topLeads = $this->leadRepository
                    ->select('title', 'lead_value as amount', 'leads.created_at', 'status', 'lead_stages.name as statusLabel')
                    ->leftJoin('lead_stages', 'leads.lead_stage_id', '=', 'lead_stages.id')
                    ->orderBy('lead_value', 'asc')
                    ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
                    ->limit(3)
                    ->get()
                    ->toArray();

        $cardData = [
            "data" => $topLeads
        ];

        return $cardData;
    }

    /**
     * Collect Stages card data.
     */
    private function getStages($startDateFilter, $endDateFilter, $totalWeeks)
    {
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
                    ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
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

        return $cardData;
    }

    /**
     * Collect Emails card data.
     */
    private function getEmails($startDateFilter, $endDateFilter, $totalWeeks)
    {
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

        return $cardData;
    }

    /**
     * Collect TopCustomers card data.
     */
    private function getTopCustomers($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $topCustomers = $this->leadRepository
                        ->select('persons.id as personId', 'persons.name as label', \DB::raw("(COUNT(*)) as count"))
                        ->leftJoin('persons', 'leads.person_id', '=', 'persons.id')
                        ->groupBy('person_id')
                        ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
                        ->limit(6)
                        ->orderBy('count', 'desc')
                        ->get()
                        ->toArray();

        $cardData = [
            "data" => $topCustomers
        ];

        return $cardData;
    }

    /**
     * Collect TopProducts card data.
     */
    private function getTopProducts($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $topProducts = app('Webkul\Lead\Repositories\LeadProductsRepository')
                        ->select('leads.title as label', \DB::raw("(COUNT(*)) as count"))
                        ->leftJoin('leads', 'lead_products.lead_id', '=', 'leads.id')
                        ->groupBy('product_id')
                        ->whereBetween('lead_products.created_at', [$startDateFilter, $endDateFilter])
                        ->get()
                        ->toArray();

        $cardData = [
            "data" => $topProducts
        ];

        return $cardData;
    }
}