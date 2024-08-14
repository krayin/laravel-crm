<?php

namespace Webkul\Admin\Helpers\Reporting;

use Webkul\Quote\Repositories\QuoteRepository;

class Quote extends AbstractReporting
{
    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(protected QuoteRepository $quoteRepository)
    {
        parent::__construct();
    }

    /**
     * Retrieves total quotes and their progress.
     */
    public function getTotalQuotesProgress(): array
    {
        return [
            'previous' => $previous = $this->getTotalQuotes($this->lastStartDate, $this->lastEndDate),
            'current'  => $current = $this->getTotalQuotes($this->startDate, $this->endDate),
            'progress' => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total quotes by date
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalQuotes($startDate, $endDate): int
    {
        return $this->quoteRepository
            ->resetModel()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }
}
