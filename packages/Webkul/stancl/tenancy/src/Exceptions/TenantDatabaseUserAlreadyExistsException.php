<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Stancl\Tenancy\Contracts\TenantCannotBeCreatedException;

class TenantDatabaseUserAlreadyExistsException extends TenantCannotBeCreatedException
{
    /** @var string */
    protected $user;

    public function reason(): string
    {
        return "Database user {$this->user} already exists.";
    }

    public function __construct(string $user)
    {
        parent::__construct();

        $this->user = $user;
    }
}
