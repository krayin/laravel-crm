<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenancyNotInitializedException;

class ScopeSessions
{
    public static $tenantIdKey = '_tenant_id';

    public function handle(Request $request, Closure $next)
    {
        if (! tenancy()->initialized) {
            throw new TenancyNotInitializedException('Tenancy needs to be initialized before the session scoping middleware is executed');
        }

        if (! $request->session()->has(static::$tenantIdKey)) {
            $request->session()->put(static::$tenantIdKey, tenant()->getTenantKey());
        } else {
            if ($request->session()->get(static::$tenantIdKey) !== tenant()->getTenantKey()) {
                abort(403);
            }
        }

        return $next($request);
    }
}
