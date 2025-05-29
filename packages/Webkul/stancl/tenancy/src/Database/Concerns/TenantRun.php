<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\Tenancy\Contracts\Tenant;

trait TenantRun
{
    /**
     * Run a callback in this tenant's context.
     * Atomic, safely reverts to previous context.
     *
     * @param callable $callback
     * @return mixed
     */
    public function run(callable $callback)
    {
        /** @var Tenant $this */
        $originalTenant = tenant();

        tenancy()->initialize($this);
        $result = $callback($this);

        if ($originalTenant) {
            tenancy()->initialize($originalTenant);
        } else {
            tenancy()->end();
        }

        return $result;
    }
}
