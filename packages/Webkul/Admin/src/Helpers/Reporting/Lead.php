<?php

namespace Webkul\Admin\Helpers\Reporting;

use Illuminate\Support\Facades\DB;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\StageRepository;

class Lead extends AbstractReporting
{
    /**
     * The channel ids.
     */
    protected array $stageIds;

    /**
     * The channel ids.
     */
    protected array $wonStageIds;

    /**
     * The channel ids.
     */
    protected array $lostStageIds;

    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(
        protected LeadRepository $leadRepository,
        protected StageRepository $stageRepository
    )
    {
        $this->wonStageIds = $this->stageRepository->where('code', 'won')->pluck('id')->toArray();

        $this->lostStageIds = $this->stageRepository->where('code', 'lost')->pluck('id')->toArray();

        parent::__construct();
    }

    /**
     * Retrieves total leads and their progress.
     */
    public function getTotalLeadsProgress(): array
    {
        return [
            'previous' => $previous = $this->getTotalLeads($this->lastStartDate, $this->lastEndDate),
            'current'  => $current = $this->getTotalLeads($this->startDate, $this->endDate),
            'progress' => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total leads by date
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalLeads($startDate, $endDate): int
    {
        return $this->leadRepository
            ->resetModel()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Retrieves total lead value and their progress.
     */
    public function getTotalLeadValueProgress(): array
    {
        return [
            'previous'        => $previous = $this->getTotalLeadValue($this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getTotalLeadValue($this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'progress'        => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total lead value
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalLeadValue($startDate, $endDate): float
    {
        return $this->orderRepository
            ->resetModel()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('lead_value');
    }

    /**
     * Retrieves average lead value and their progress.
     */
    public function getAverageLeadValueProgress(): array
    {
        return [
            'previous'        => $previous = $this->getAverageLeadValue($this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getAverageLeadValue($this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'progress'        => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves average lead value
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getAverageLeadValue($startDate, $endDate): float
    {
        return $this->orderRepository
            ->resetModel()
            ->whereIn('channel_id', $this->channelIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('lead_value');
    }

    /**
     * Retrieves average won lead value and their progress.
     */
    public function getAverageWonLeadValueProgress(): array
    {
        return [
            'previous'        => $previous = $this->getAverageWonLeadValue($this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getAverageWonLeadValue($this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'progress'        => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves average won lead value
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    public function getAverageWonLeadValue($startDate, $endDate): ?float
    {
        return $this->orderRepository
            ->resetModel()
            ->whereIn('lead_pipeline_stage_id', $this->wonStageIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('lead_value');
    }

    /**
     * Retrieves average lost lead value and their progress.
     */
    public function getAverageLostLeadValueProgress(): array
    {
        return [
            'previous'        => $previous = $this->getAverageLostLeadValue($this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getAverageLostLeadValue($this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'progress'        => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves average lost lead value
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    public function getAverageLostLeadValue($startDate, $endDate): ?float
    {
        return $this->orderRepository
            ->resetModel()
            ->whereIn('lead_pipeline_stage_id', $this->lostStageIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('lead_value');
    }

    /**
     * Returns over time stats.
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  string  $valueColumn
     * @param  string  $period
     */
    public function getOverTimeStats($startDate, $endDate, $valueColumn, $dateColumn = 'created_at', $period = 'auto'): array
    {
        $config = $this->getTimeInterval($startDate, $endDate, $dateColumn, $period);

        $groupColumn = $config['group_column'];

        $results = $this->orderRepository
            ->resetModel()
            ->select(
                DB::raw("$groupColumn AS date"),
                DB::raw("$valueColumn AS total"),
                DB::raw('COUNT(*) AS count')
            )
            ->whereIn('lead_pipeline_stage_id', $this->stageIds)
            ->whereBetween($dateColumn, [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        foreach ($config['intervals'] as $interval) {
            $total = $results->where('date', $interval['filter'])->first();

            $stats[] = [
                'label' => $interval['start'],
                'total' => $total?->total ?? 0,
                'count' => $total?->count ?? 0,
            ];
        }

        return $stats ?? [];
    }
}
