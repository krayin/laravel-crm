<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Resolvers;
use Stancl\Tenancy\Resolvers\Contracts\CachedTenantResolver;

trait InvalidatesResolverCache
{
    public static $resolvers = [
        Resolvers\DomainTenantResolver::class,
        Resolvers\PathTenantResolver::class,
        Resolvers\RequestDataTenantResolver::class,
    ];

    public static function bootInvalidatesResolverCache()
    {
        $invalidateCache = static function (Tenant $tenant) {
            foreach (static::$resolvers as $resolver) {
                /** @var CachedTenantResolver $resolver */
                $resolver = app($resolver);

                $resolver->invalidateCache($tenant);
            }
        };

        static::saved($invalidateCache);
        static::deleting($invalidateCache);
    }
}
