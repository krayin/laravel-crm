<?php

namespace Webkul\Admin\Traits\Dashboard;

use Carbon\Carbon;

trait DataRetrival
{
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
                            "data"              => $wonLeadsCount,
                            "label"             => "Won",
                            "backgroundColor"   => "#4BC0C0",
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
     * Collect leads card data.
     */
    private function getLeadsStarted($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $labels = $leadsStarted = [];

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
                array_push($leadsStarted, $this->leadRepository->getLeadsCount("all", $startDate, $endDate));
            }
        } else {
            $labels = [__("admin::app.dashboard.week") . "1"];
            
            $leadsStarted = [$this->leadRepository->getLeadsCount("Won", $startDateFilter, $endDateFilter)];
        }

        if (! empty(array_filter($leadsStarted))) {
            $cardData = [
                "data" => [
                    "labels" => $labels,
                    "datasets" => [
                        [
                            "fill"              => true,
                            "tension"           => 0.6,
                            "backgroundColor"   => "#4BC0C0",
                            "borderColor"       => '#2f7373',
                            "data"              => $leadsStarted,
                            "label"             => __("admin::app.dashboard.leads_started"),
                        ],
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
                            "fill"              => true,
                            "tension"           => 0.6,
                            "backgroundColor"   => "#4BC0C0",
                            "borderColor"       => '#2f7373',
                            "data"              => $productsCount,
                            "label"             => __("admin::app.dashboard.products"),
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
                            "fill"              => true,
                            "tension"           => 0.6,
                            "backgroundColor"   => "#4BC0C0",
                            "borderColor"       => '#2f7373',
                            "data"              => $customersCount,
                            "label"             => __("admin::app.dashboard.customers"),
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
                    ->orderBy('lead_value', 'desc')
                    ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
                    ->where(function ($query) {
                        $currentUser = auth()->guard('user')->user();

                        if ($currentUser->lead_view_permission != 'global') {
                            if ($currentUser->lead_view_permission == 'group') {
                                $query->whereIn('leads.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
                            } else {
                                $query->where('leads.user_id', $currentUser->id);
                            }
                        }
                    })
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
                'label'     => $stage['name'],
                'count'     => $leadsCount,
                'bar_type'  => $barType,
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
                            ->get();
        
        foreach ($emailsCollection as $key => $email) {
            if (in_array('inbox', $email->folders) !== false) {
                $receivedEmails++;
            } else if (in_array('draft', $email->folders) !== false) {
                $draftEmails++;
            } else if (in_array('outbox', $email->folders) !== false) {
                $outboxEmails++;
            } else if (in_array('sent', $email->folders) !== false) {
                $sentEmails++;
            } else if (in_array('trash', $email->folders) !== false) {
                $trashEmails++;
            }

            $totalEmails++;
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
                        ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
                        ->groupBy('person_id')
                        ->orderBy('lead_value', 'desc')
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
        $topProducts = app('Webkul\Lead\Repositories\ProductRepository')
                        ->select('leads.title as label', \DB::raw("(COUNT(*)) as count"))
                        ->leftJoin('leads', 'lead_products.lead_id', '=', 'leads.id')
                        ->groupBy('product_id')
                        ->whereBetween('lead_products.created_at', [$startDateFilter, $endDateFilter])
                        ->limit(6)
                        ->get()
                        ->toArray();

        $cardData = [
            "data" => $topProducts
        ];

        return $cardData;
    }

    /**
     * Collect quotes card data.
     */
    private function getQuotes($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $labels = $quotes = [];

        $quotesRepository = app('Webkul\Quote\Repositories\QuoteRepository');

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

                // get quotes count
                array_push($quotes, $quotesRepository->getQuotesCount("all", $startDate, $endDate));
            }
        } else {
            $labels = [__("admin::app.dashboard.week") . "1"];
            
            $quotes = [$quotesRepository->getQuotesCount("Won", $startDateFilter, $endDateFilter)];
        }

        if (! empty(array_filter($quotes))) {
            $cardData = [
                "data" => [
                    "labels" => $labels,
                    "datasets" => [
                        [
                            "fill"              => true,
                            "tension"           => 0.6,
                            "backgroundColor"   => "#4BC0C0",
                            "borderColor"       => '#2f7373',
                            "data"              => $quotes,
                            "label"             => __("admin::app.dashboard.leads_started"),
                        ],
                    ]
                ]
            ];
        }

        return $cardData ?? false;
    }
}