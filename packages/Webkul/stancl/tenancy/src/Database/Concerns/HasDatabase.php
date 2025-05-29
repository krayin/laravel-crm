<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\DatabaseConfig;

trait HasDatabase
{
    public function database(): DatabaseConfig
    {
        /** @var TenantWithDatabase $this */

        return new DatabaseConfig($this);
    }
}
