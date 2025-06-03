<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Exception;

class TenantDatabaseDoesNotExistException extends Exception
{
    public function __construct($database)
    {
        parent::__construct("Database $database does not exist.");
    }
}
