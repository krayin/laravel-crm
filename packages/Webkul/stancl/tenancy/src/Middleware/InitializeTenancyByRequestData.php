<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Contracts\TenantResolver;
use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByRequestData extends IdentificationMiddleware
{
    /** @var string|null */
    public static $header = 'X-Tenant';

    /** @var string|null */
    public static $queryParameter = 'tenant';

    /** @var callable|null */
    public static $onFail;

    /** @var Tenancy */
    protected $tenancy;

    /** @var TenantResolver */
    protected $resolver;

    public function __construct(Tenancy $tenancy, RequestDataTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() !== 'OPTIONS') {
            return $this->initializeTenancy($request, $next, $this->getPayload($request));
        }

        return $next($request);
    }

    protected function getPayload(Request $request): ?string
    {
        $tenant = null;
        if (static::$header && $request->hasHeader(static::$header)) {
            $tenant = $request->header(static::$header);
        } elseif (static::$queryParameter && $request->has(static::$queryParameter)) {
            $tenant = $request->get(static::$queryParameter);
        }

        return $tenant;
    }
}
