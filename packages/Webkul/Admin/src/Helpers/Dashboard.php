<?php

namespace Webkul\Admin\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Email\Repositories\EmailRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\ProductRepository as LeadProductRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Quote\Repositories\QuoteRepository;
use Webkul\User\Repositories\UserRepository;

class Dashboard
{
    /**
     * Cards.
     *
     * @var array
     */
    protected $cards;

    /**
     * Lead repository instance.
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * Pipeline repository instance.
     *
     * @var \Webkul\Lead\Repositories\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * Product repository instance.
     *
     * @var \Webkul\Lead\Repositories\ProductRepository
     */
    protected $leadProductRepository;

    /**
     * Quote repository instance.
     *
     * @var \Webkul\Quote\Repositories\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * Product repository instance.
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Person repository instance.
     *
     * @var \Webkul\Contact\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * Activity repository instance.
     *
     * @var \Webkul\Activity\Repositories\ActivityRepository
     */
    protected $activityRepository;

    /**
     * User repository instance.
     *
     * @var \Webkul\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Email repository instance.
     *
     * @var \Webkul\Email\Repositories\EmailRepository
     */
    protected $emailRepository;

    /**
     * Create a new helper instance.
     *
     * @param  \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param  \Webkul\Lead\Repositories\PipelineRepository  $pipelineRepository
     * @param  \Webkul\Lead\Repositories\ProductRepository  $leadProductRepository
     * @param  \Webkul\Quote\Repositories\QuoteRepository  $quoteRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Product\Repositories\PersonRepository  $personRepository
     * @param  \Webkul\Product\Repositories\ActivityRepository  $activityRepository
     * @param  \Webkul\Product\Repositories\UserRepository  $userRepository
     * @param  \Webkul\Email\Repositories\EmailRepository  $emailRepository
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        PipelineRepository $pipelineRepository,
        LeadProductRepository $leadProductRepository,
        QuoteRepository $quoteRepository,
        ProductRepository $productRepository,
        PersonRepository $personRepository,
        ActivityRepository $activityRepository,
        UserRepository $userRepository,
        EmailRepository $emailRepository
    ) {
        $this->leadRepository = $leadRepository;

        $this->pipelineRepository = $pipelineRepository;

        $this->leadProductRepository = $leadProductRepository;

        $this->quoteRepository = $quoteRepository;

        $this->productRepository = $productRepository;

        $this->personRepository = $personRepository;

        $this->activityRepository = $activityRepository;

        $this->userRepository = $userRepository;

        $this->emailRepository = $emailRepository;
    }

    /**
     * This will set all available cards data to be displayed on dashboard.
     *
     * @return void
     */
    public function setCards()
    {
        $this->cards = array_map(function ($card) {
            if (isset($card['label'])) {
                $card['label'] = trans($card['label']);
            }

            return $card;
        }, config('dashboard_cards'));
    }

    /**
     * This will set all available cards data to be displayed on dashboard.
     *
     * @return array
     */
    public function getCards(): array
    {
        if(! $this->cards) {
            $this->setCards();
        }

        return $this->cards;
    }

    /**
     * Collect leads card data.
     *
     * @return array|boolean
     */
    public function getLeads($startDateFilter, $endDateFilter, $totalWeeks)
    {
        if (! bouncer()->hasPermission('leads')) {
            return 0;
        }

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
                    "labels"   => $labels,
                    "datasets" => [
                        [
                            "data"            => $wonLeadsCount,
                            "label"           => __("admin::app.settings.pipelines.won-stage"),
                            "backgroundColor" => "#4BC0C0",
                        ], [
                            "backgroundColor" => "#FF4D50",
                            "data"            => $lostLeadsCount,
                            "label"           => __("admin::app.settings.pipelines.lost-stage"),
                        ]
                    ]
                ]
            ];
        }

        return $cardData ?? false;
    }

    /**
     * Collect leads card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array|boolean
     */
    public function getLeadsStarted($startDateFilter, $endDateFilter, $totalWeeks)
    {
        if (! bouncer()->hasPermission('leads')) {
            return 0;
        }
        
        $labels = $leadsStarted = [];

        if ($totalWeeks) {
            for ($index = $totalWeeks; $index >= 1; $index--) {
                list(
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'labels'    => $labels,
                ) = $this->getFormattedDateRange([
                    "start_date"  => $startDateFilter,
                    "end_date"    => $endDateFilter,
                    "index"       => $index,
                    "labels"      => $labels,
                    "total_weeks" => $totalWeeks,
                ]);

                array_push($leadsStarted, $this->leadRepository->getLeadsCount("all", $startDate, $endDate));
            }
        } else {
            $labels = [__("admin::app.dashboard.week") . "1"];

            $leadsStarted = [$this->leadRepository->getLeadsCount("Won", $startDateFilter, $endDateFilter)];
        }

        if (! empty(array_filter($leadsStarted))) {
            $cardData = [
                "data" => [
                    "labels"   => $labels,
                    "datasets" => [
                        [
                            "fill"            => true,
                            "tension"         => 0.6,
                            "backgroundColor" => "#4BC0C0",
                            "borderColor"     => '#2f7373',
                            "data"            => $leadsStarted,
                            "label"           => __("admin::app.dashboard.leads_started"),
                        ],
                    ]
                ]
            ];
        }

        return $cardData ?? false;
    }

    /**
     * Collect Products card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array|boolean
     */
    public function getProducts($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $labels = $productsCount = [];

        if ($totalWeeks) {
            for ($index = $totalWeeks; $index >= 1; $index--) {
                list(
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'labels'    => $labels,
                ) = $this->getFormattedDateRange([
                    "start_date"  => $startDateFilter,
                    "end_date"    => $endDateFilter,
                    "index"       => $index,
                    "labels"      => $labels,
                    "total_weeks" => $totalWeeks,
                ]);

                // get products count
                array_push($productsCount, $this->productRepository->getProductCount($startDate, $endDate));
            }
        } else {
            $labels = [__("admin::app.dashboard.week") . "1"];
            $productsCount = [$this->productRepository->getProductCount($startDateFilter, $endDateFilter)];
        }

        if (! empty(array_filter($productsCount))) {
            $cardData = [
                "data" => [
                    "labels"   => $labels,
                    "datasets" => [
                        [
                            "fill"            => true,
                            "tension"         => 0.6,
                            "backgroundColor" => "#4BC0C0",
                            "borderColor"     => '#2f7373',
                            "data"            => $productsCount,
                            "label"           => __("admin::app.dashboard.products"),
                        ],
                    ]
                ]
            ];
        }

        return $cardData ?? false;
    }

    /**
     * Collect customers card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array|boolean
     */
    public function getCustomers($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $labels = $customersCount = [];

        if ($totalWeeks) {
            for ($index = $totalWeeks; $index >= 1; $index--) {
                list(
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'labels'    => $labels,
                ) = $this->getFormattedDateRange([
                    "start_date"  => $startDateFilter,
                    "end_date"    => $endDateFilter,
                    "index"       => $index,
                    "labels"      => $labels,
                    "total_weeks" => $totalWeeks,
                ]);

                // get customers count
                array_push($customersCount, $this->personRepository->getCustomerCount($startDate, $endDate));
            }
        } else {
            $labels = [__("admin::app.dashboard.week") . "1"];
            $customersCount = [$this->personRepository->getCustomerCount($startDateFilter, $endDateFilter)];
        }

        if (! empty(array_filter($customersCount))) {
            $cardData = [
                "data" => [
                    "labels"   => $labels,
                    "datasets" => [
                        [
                            "fill"            => true,
                            "tension"         => 0.6,
                            "backgroundColor" => "#4BC0C0",
                            "borderColor"     => '#2f7373',
                            "data"            => $customersCount,
                            "label"           => __("admin::app.dashboard.customers"),
                        ],
                    ]
                ]
            ];
        }

        return $cardData ?? false;
    }

    /**
     * Collect activity card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getActivities($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $activityTypes = ['call', 'meeting', 'lunch'];

        $activities = $this->activityRepository
            ->select(DB::raw("(COUNT(DISTINCT activities.id)) as count"), 'type as label')
            ->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->whereIn('type', $activityTypes)
            ->whereBetween('created_at', [$startDateFilter, $endDateFilter])
            ->where(function ($query) {
                $currentUser = auth()->guard('user')->user();

                if ($currentUser->view_permission != 'global') {
                    if ($currentUser->view_permission == 'group') {
                        $userIds = app(UserRepository::class)->getCurrentUserGroupsUserIds();

                        $query->whereIn('activities.user_id', $userIds)
                            ->orWhereIn('activity_participants.user_id', $userIds);
                    } else {
                        $query->where('activities.user_id', $currentUser->id)
                            ->orWhere('activity_participants.user_id', $currentUser->id);
                    }
                }
            })
            ->get();

        $typesWithZeroCount = collect($activityTypes)
            ->filter(function ($type) use ($activities) {
                return ! in_array($type, $activities->pluck('label')->toArray());
            })
            ->map(function ($type) { 
                return [
                    'count' => 0,
                    'label' => __("admin::app.activities.$type")
                ];
            });

        $activities->push(...$typesWithZeroCount);

        return [
            'data'  => $activities,
            'total' => $activities->pluck('count')->sum()
        ];
    }

    /**
     * Collect top leads card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getTopLeads($startDateFilter, $endDateFilter, $totalWeeks)
    {   
        if (! bouncer()->hasPermission('leads')) {
            return 0;
        }
       
        $topLeads = $this->leadRepository
            ->select('leads.id', 'title', 'lead_value as amount', 'leads.created_at', 'status', 'lead_pipeline_stages.name as statusLabel')
            ->leftJoin('lead_pipeline_stages', 'leads.lead_pipeline_stage_id', '=', 'lead_pipeline_stages.id')
            ->orderBy('lead_value', 'desc')
            ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
            ->where(function ($query) {
            $currentUser = auth()->guard('user')->user();

            if ($currentUser->view_permission != 'global') {
                if ($currentUser->view_permission == 'group') {
                    $query->whereIn('leads.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
                } else {
                    $query->where('leads.user_id', $currentUser->id);
                }
            }
        })
        ->limit(3)
        ->get();

        $cardData = [
            "data" => $topLeads,
        ];

        return $cardData;
    }

    /**
     * Collect pipelines card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getPipelines($startDateFilter, $endDateFilter, $totalWeeks): array
    {
        $totalLeadsBetweenDateCount = $this->leadRepository
            ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
            ->where(function ($query) {
                $currentUser = auth()->guard('user')->user();

                if ($currentUser->view_permission != 'global') {
                    if ($currentUser->view_permission == 'group') {
                        $query->whereIn('leads.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
                    } else {
                        $query->where('leads.user_id', $currentUser->id);
                    }
                }
            })
            ->count();

        $leadPipelines = $this->pipelineRepository
            ->select('id', 'name')
            ->get()
            ->map(function ($pipeline) use ($startDateFilter, $endDateFilter) {
                $individualLeads = $this->leadRepository
                    ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id')
                    ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
                    ->where('lead_pipelines.id', $pipeline->id)
                    ->where(function ($query) {
                        $currentUser = auth()->guard('user')->user();
        
                        if ($currentUser->view_permission != 'global') {
                            if ($currentUser->view_permission == 'group') {
                                $query->whereIn('leads.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
                            } else {
                                $query->where('leads.user_id', $currentUser->id);
                            }
                        }
                    })
                    ->count();

                return [
                    'label' => $pipeline->name,
                    'count' => $individualLeads,
                ];
            });

        return [
            'data'  => $leadPipelines,
            'total' => $totalLeadsBetweenDateCount
        ];
    }

    /**
     * Collect emails card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getEmails($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $totalEmails = $receivedEmails = $draftEmails = $outboxEmails = $sentEmails = $trashEmails = 0;

        $emailsCollection = $this->emailRepository
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
     * Collect top customers card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getTopCustomers($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $topCustomers = $this->leadRepository
            ->select('persons.id as personId', 'persons.name as label', DB::raw("(COUNT(*)) as count"))
            ->leftJoin('persons', 'leads.person_id', '=', 'persons.id')
            ->whereBetween('leads.created_at', [$startDateFilter, $endDateFilter])
            ->groupBy('person_id')
            ->orderBy('lead_value', 'desc')
            ->limit(6)
            ->orderBy('count', 'desc')
            ->get();

        $cardData = [
            "data" => $topCustomers
        ];

        return $cardData;
    }

    /**
     * Collect top products card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getTopProducts($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $topProducts = $this->leadProductRepository
            ->select('lead_products.*', 'products.name as label', DB::raw("(COUNT(*)) as count"))
            ->leftJoin('leads', 'lead_products.lead_id', '=', 'leads.id')
            ->leftJoin('products', 'lead_products.product_id', '=', 'products.id')
            ->groupBy('lead_products.product_id')
            ->whereBetween('lead_products.created_at', [$startDateFilter, $endDateFilter])
            ->limit(6)
            ->get();

        $cardData = [
            "data" => $topProducts
        ];

        return $cardData;
    }

    /**
     * Collect quotes card data.
     *
     * @param  string  $startDateFilter
     * @param  string  $endDateFilter
     * @param  array  $totalWeeks
     * @return array
     */
    public function getQuotes($startDateFilter, $endDateFilter, $totalWeeks)
    {
        $labels = $quotes = [];

        if ($totalWeeks) {
            for ($index = $totalWeeks; $index >= 1; $index--) {
                list(
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'labels'    => $labels,
                ) = $this->getFormattedDateRange([
                    "start_date"  => $startDateFilter,
                    "end_date"    => $endDateFilter,
                    "index"       => $index,
                    "labels"      => $labels,
                    "total_weeks" => $totalWeeks,
                ]);

                // get quotes count
                array_push($quotes, $this->quoteRepository->getQuotesCount($startDate, $endDate));
            }
        } else {
            $labels = [__("admin::app.dashboard.week") . "1"];

            $quotes = [$this->quoteRepository->getQuotesCount("Won", $startDateFilter, $endDateFilter)];
        }

        if (! empty(array_filter($quotes))) {
            $cardData = [
                "data" => [
                    "labels" => $labels,
                    "datasets" => [
                        [
                            "fill"            => true,
                            "tension"         => 0.6,
                            "backgroundColor" => "#4BC0C0",
                            "borderColor"     => '#2f7373',
                            "data"            => $quotes,
                            "label"           => __("admin::app.dashboard.leads_started"),
                        ],
                    ]
                ]
            ];
        }

        return $cardData ?? false;
    }

    /**
     * This will return date range to be applied on dashboard data.
     *
     * @param  array  $data
     * @return array
     */
    public function getDateRangeDetails($data)
    {
        $cardId = $data['card-id'];

        $dateRange = $data['date-range'] ?? Carbon::now()->subMonth()->addDays(1)->format('Y-m-d') . "," . Carbon::now()->format('Y-m-d');
        $dateRange = explode(",", $dateRange);

        $startDateFilter = $dateRange[0] . ' ' . Carbon::parse('00:01')->format('H:i');
        $endDateFilter = $dateRange[1] . ' ' . Carbon::parse('23:59')->format('H:i');

        $startDate = Carbon::parse($startDateFilter);
        $endDate = Carbon::parse($endDateFilter);

        $totalWeeks = ceil($startDate->floatDiffInWeeks($endDate));

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
     * Format dates of filter.
     *
     * @param  array  $data
     * @return array
     */
    public function getFormattedDateRange($data)
    {
        $labels = $data['labels'];
        $currentIndex = $data['index'];
        $totalWeeks = $data['total_weeks'];

        $startDate = Carbon::parse($data["start_date"]);
        $endDate = Carbon::parse($data["end_date"]);

        array_push($labels, __("admin::app.dashboard.week") . (($totalWeeks + 1) - $currentIndex));

        $startDate = $currentIndex != $totalWeeks
            ? $startDate->addDays((7 * ($totalWeeks - $currentIndex)) + ($totalWeeks - $currentIndex))
            : $startDate->addDays(7 * ($totalWeeks - $currentIndex));

        $endDate = $currentIndex == 1 ? $endDate->addDays(1) : (clone $startDate)->addDays(7);

        $startDate = $startDate->format('Y-m-d  00:00:01');
        $endDate = $endDate->format('Y-m-d 23:59:59');

        return compact('startDate', 'endDate', 'labels');
    }

    /**
     * Collect card data based on `cardId`.
     *
     * @param  array  $requestData
     * @return array|boolean
     */
    public function getFormattedCardData($requestData)
    {
        $relevantFunction = false;

        list(
            'cardId'          => $cardId,
            'endDate'         => $endDate,
            'startDate'       => $startDate,
            'totalWeeks'      => $totalWeeks,
            'endDateFilter'   => $endDateFilter,
            'startDateFilter' => $startDateFilter,
        ) = $this->getDateRangeDetails($requestData);

        foreach ($this->getCards() as $card) {
            if (isset($card['card_id']) && $card['card_id'] == $cardId) {
                if (isset($card['class_name'])) {
                    $class = app($card['class_name']);
                }

                if (isset($card['method_name'])) {
                    $relevantFunction = $card['method_name'];
                }
            }
        }

        $class = $class ?? $this;

        if (! $relevantFunction) {
            $relevantFunction = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $cardId)));
        }

        if (! method_exists($class ?? $this, $relevantFunction)) {
            $relevantFunction = false;
        }

        $cardData = $relevantFunction
            ? $class->{$relevantFunction}(
                $startDateFilter,
                $endDateFilter,
                $totalWeeks
            )
            : $cardData ?? false;

        return $cardData;
    }
}
