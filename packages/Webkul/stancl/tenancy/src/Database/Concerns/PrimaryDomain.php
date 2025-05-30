<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

/**
 * @property-read string $primary_domain_hostname
 */
trait PrimaryDomain
{
    // This string should usually come from a relationship implemented by you.
    abstract public function getPrimaryDomainHostnameAttribute(): string;

    public function route($route, $parameters = [], $absolute = true)
    {
        return tenant_route($this->primary_domain_hostname, $route, $parameters, $absolute);
    }
}
