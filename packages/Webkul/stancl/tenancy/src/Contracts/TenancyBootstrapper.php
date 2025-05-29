<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Contracts;

/**
 * TenancyBootstrappers are classes that make your application tenant-aware automatically.
 */
interface TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant);

    public function revert();
}
