<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Stancl\Tenancy\Exceptions\TenancyNotInitializedException;
use Symfony\Component\HttpFoundation\IpUtils;

class CheckTenantForMaintenanceMode extends CheckForMaintenanceMode
{
    public function handle($request, Closure $next)
    {
        if (! tenant()) {
            throw new TenancyNotInitializedException;
        }

        if (tenant('maintenance_mode')) {
            $data = tenant('maintenance_mode');

            if (isset($data['allowed']) && IpUtils::checkIp($request->ip(), (array) $data['allowed'])) {
                return $next($request);
            }

            if ($this->inExceptArray($request)) {
                return $next($request);
            }

            throw new HttpException(
                503,
                'Service Unavailable',
                null,
                isset($data['retry']) ? ['Retry-After' => $data['retry']] : []
            );
        }

        return $next($request);
    }
}
