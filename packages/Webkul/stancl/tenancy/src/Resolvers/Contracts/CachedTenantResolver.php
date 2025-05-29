<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Resolvers\Contracts;

use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Contracts\TenantResolver;

abstract class CachedTenantResolver implements TenantResolver
{
    /** @var bool */
    public static $shouldCache = false;

    /** @var int */
    public static $cacheTTL = 3600; // seconds

    /** @var string|null */
    public static $cacheStore = null; // default

    /** @var Repository */
    protected $cache;

    public function __construct(Factory $cache)
    {
        $this->cache = $cache->store(static::$cacheStore);
    }

    public function resolve(...$args): Tenant
    {
        if (! static::$shouldCache) {
            return $this->resolveWithoutCache(...$args);
        }

        $key = $this->getCacheKey(...$args);

        if ($tenant = $this->cache->get($key)) {
            $this->resolved($tenant, ...$args);

            return $tenant;
        }

        $tenant = $this->resolveWithoutCache(...$args);
        $this->cache->put($key, $tenant, static::$cacheTTL);

        return $tenant;
    }

    public function invalidateCache(Tenant $tenant): void
    {
        if (! static::$shouldCache) {
            return;
        }

        foreach ($this->getArgsForTenant($tenant) as $args) {
            $this->cache->forget($this->getCacheKey(...$args));
        }
    }

    public function getCacheKey(...$args): string
    {
        return '_tenancy_resolver:' . static::class . ':' . json_encode($args);
    }

    abstract public function resolveWithoutCache(...$args): Tenant;

    public function resolved(Tenant $tenant, ...$args): void
    {
    }

    /**
     * Get all the arg combinations for resolve() that can be used to find this tenant.
     *
     * @param Tenant $tenant
     * @return array[]
     */
    abstract public function getArgsForTenant(Tenant $tenant): array;
}
