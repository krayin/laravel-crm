<?php

namespace Webkul\Admin\Traits\Dashboard;

use Carbon\Carbon;

trait Helper
{
    use DataRetrival;

    /**
     * this will set all available cards data to be displayed on dashboard.
     */
    private function setCardsData()
    {
        $this->cards = [
            [
                "selected"      => true,
                "card_id"       => "leads",
                "card_type"     => "bar_chart",
                "label"         => __('admin::app.dashboard.leads_over_time'),
            ], [
                "selected"      => true,
                "card_id"       => "leads_started",
                "card_type"     => "line_chart",
                "label"         => __('admin::app.dashboard.leads_started'),
            ], [
                "selected"      => true,
                "card_id"       => "activities",
                "card_type"     => "activities",
                "label"         => __('admin::app.dashboard.activities'),
            ], [
                "selected"      => true,
                "card_id"       => "top_leads",
                "card_type"     => "top_card",
                "label"         => __('admin::app.dashboard.top_leads'),
            ], [
                "selected"      => true,
                "card_id"       => "stages",
                "card_type"     => "stages_bar",
                "label"         => __('admin::app.dashboard.stages'),
            ], [
                "selected"      => true,
                "card_id"       => "emails",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.emails'),
            ], [
                "selected"      => true,
                "card_id"       => "customers",
                "card_type"     => "line_chart",
                "label"         => __('admin::app.dashboard.customers'),
            ], [
                "selected"      => true,
                "card_id"       => "top_customers",
                "card_type"     => "emails",
                "label"         => __('admin::app.dashboard.top_customers'),
            ], [
                "selected"      => true,
                "card_id"       => "products",
                "card_type"     => "line_chart",
                "label"         => __('admin::app.dashboard.products'),
            ], [
                "selected"      => true,
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
        $currentIndex = $data['index'];
        $totalWeeks = $data['total_weeks'];

        $startDate = Carbon::parse($data["start_date"]);
        $endDate = Carbon::parse($data["end_date"]);

        array_push($labels, __("admin::app.dashboard.week") . (($totalWeeks + 1) - $currentIndex));
        
        $startDate = $currentIndex != $totalWeeks
                    ? $startDate->addDays((7 * ($totalWeeks - $currentIndex)) + ($totalWeeks - $currentIndex))
                    : $startDate->addDays(7 * ($totalWeeks - $currentIndex));

        $endDate = $currentIndex == 1 ? $endDate->addDays(1) : (clone $startDate)->addDays(7);
        
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

        $relevantFunction = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $cardId)));

        if (! method_exists($this, $relevantFunction)) {
            $relevantFunction = false;
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
}