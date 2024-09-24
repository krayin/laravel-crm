<?php

namespace Webkul\Admin\Helpers\Reporting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Contact\Repositories\PersonRepository;

class Person extends AbstractReporting
{
    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository)
    {
        parent::__construct();
    }

    /**
     * Retrieves total persons and their progress.
     */
    public function getTotalPersonsProgress(): array
    {
        return [
            'previous' => $previous = $this->getTotalPersons($this->lastStartDate, $this->lastEndDate),
            'current'  => $current = $this->getTotalPersons($this->startDate, $this->endDate),
            'progress' => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total persons by date
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalPersons($startDate, $endDate): int
    {
        return $this->personRepository
            ->resetModel()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Gets top customers by revenue.
     *
     * @param  int  $limit
     */
    public function getTopCustomersByRevenue($limit = null): Collection
    {
        $tablePrefix = DB::getTablePrefix();

        $items = $this->personRepository
            ->resetModel()
            ->leftJoin('leads', 'persons.id', '=', 'leads.person_id')
            ->select('*', 'persons.id as id')
            ->addSelect(DB::raw('SUM('.$tablePrefix.'leads.lead_value) as revenue'))
            ->whereBetween('leads.closed_at', [$this->startDate, $this->endDate])
            ->having(DB::raw('SUM('.$tablePrefix.'leads.lead_value)'), '>', 0)
            ->groupBy('person_id')
            ->orderBy('revenue', 'DESC')
            ->limit($limit)
            ->get();

        $items = $items->map(function ($item) {
            return [
                'id'                => $item->id,
                'name'              => $item->name,
                'emails'            => $item->emails,
                'contact_numbers'   => $item->contact_numbers,
                'revenue'           => $item->revenue,
                'formatted_revenue' => core()->formatBasePrice($item->revenue),
            ];
        });

        return $items;
    }
}
