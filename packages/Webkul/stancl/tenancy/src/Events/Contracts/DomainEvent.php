<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Contracts\Domain;

abstract class DomainEvent
{
    use SerializesModels;

    /** @var Domain */
    public $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }
}
