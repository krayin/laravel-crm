<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Contracts;

use Stancl\Tenancy\DatabaseConfig;

interface TenantWithDatabase extends Tenant
{
    public function database(): DatabaseConfig;

    /** Get an internal key. */
    public function getInternal(string $key);
}
