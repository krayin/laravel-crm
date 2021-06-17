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
        $this->cards = array_map(function ($card) {
            if (isset($card['label'])) {
                $card['label'] = trans($card['label']);
            }
            
            return $card;
        }, config('dashboard_cards'));
    }

    /**
     * This will return date range to be applied on dashboard data.
     */
    private function getDateRangeDetails($data)
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

        foreach ($this->cards as $card) {
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