<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

trait TenantConnection
{
    public function getConnectionName()
    {
        return 'tenant';
    }
}
