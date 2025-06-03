<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Events\Contracts;

use Stancl\Tenancy\Tenancy;

abstract class TenancyEvent
{
    /** @var Tenancy */
    public $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }
}
