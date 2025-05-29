<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Exception;

class NotASubdomainException extends Exception
{
    public function __construct(string $hostname)
    {
        parent::__construct("Hostname $hostname does not include a subdomain.");
    }
}
