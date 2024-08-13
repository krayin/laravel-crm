<?php

namespace Webkul\Admin\Helpers\Reporting;

use Webkul\Contact\Repositories\OrganizationRepository;

class Organization extends AbstractReporting
{
    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository)
    {
        parent::__construct();
    }

    /**
     * Retrieves total organizations and their progress.
     */
    public function getTotalOrganizationsProgress(): array
    {
        return [
            'previous' => $previous = $this->getTotalOrganizations($this->lastStartDate, $this->lastEndDate),
            'current'  => $current = $this->getTotalOrganizations($this->startDate, $this->endDate),
            'progress' => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total organizations by date
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalOrganizations($startDate, $endDate): int
    {
        return $this->organizationRepository
            ->resetModel()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }
}