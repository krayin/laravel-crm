<?php

declare(strict_types=1);

namespace Stancl\Tenancy;

use Illuminate\Cache\CacheManager as BaseCacheManager;

class CacheManager extends BaseCacheManager
{
    /**
     * Add tags and forward the call to the inner cache store.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $tags = [config('tenancy.cache.tag_base') . tenant()->getTenantKey()];

        if ($method === 'tags') {
            $count = count($parameters);
            
            if ($count !== 1) {
                throw new \Exception("Method tags() takes exactly 1 argument. $count passed.");
            }

            $names = array_values($parameters)[0];
            $names = (array) $names; // cache()->tags('foo') https://laravel.com/docs/5.7/cache#removing-tagged-cache-items

            return $this->store()->tags(array_merge($tags, $names));
        }

        return $this->store()->tags($tags)->$method(...$parameters);
    }
}
