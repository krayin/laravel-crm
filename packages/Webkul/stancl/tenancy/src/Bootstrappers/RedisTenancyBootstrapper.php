<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Bootstrappers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Redis;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class RedisTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var array<string, string> Original prefixes of connections */
    public $originalPrefixes = [];

    /** @var Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function bootstrap(Tenant $tenant)
    {
        foreach ($this->prefixedConnections() as $connection) {
            $prefix = $this->config['tenancy.redis.prefix_base'] . $tenant->getTenantKey();
            $client = Redis::connection($connection)->client();

            $this->originalPrefixes[$connection] = $client->getOption(\Redis::OPT_PREFIX);
            $client->setOption(\Redis::OPT_PREFIX, $prefix);
        }
    }

    public function revert()
    {
        foreach ($this->prefixedConnections() as $connection) {
            $client = Redis::connection($connection)->client();

            $client->setOption(\Redis::OPT_PREFIX, $this->originalPrefixes[$connection]);
        }

        $this->originalPrefixes = [];
    }

    protected function prefixedConnections()
    {
        return $this->config['tenancy.redis.prefixed_connections'];
    }
}
