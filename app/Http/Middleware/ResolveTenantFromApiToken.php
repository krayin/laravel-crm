<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant; 
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Tenancy; 
use Illuminate\Support\Facades\Auth;

class ResolveTenantFromApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => trans('rest-api::app.common.auth.token.missing')], 401);
        }

        $tenant = Tenant::where('api_token', hash('sha256', $token))->first();
        if ($tenant) {
            if (!tenancy()->initialized) {
                tenancy()->initialize($tenant);
            }
            $request->attributes->set('auth_type', 'tenant');
            return $next($request);
        }

        $user = Auth::guard('sanctum')->user();

        if ($user) {
            if ($user->is_super) {
                $request->setUserResolver(fn () => $user); 
                $request->attributes->set('auth_type', 'user');
                return $next($request);
            } else {
                return response()->json(['message' => trans('rest-api::app.common.forbidden-error')], 401);
            }
        }


        return response()->json(['message' => trans('rest-api::app.common.auth.token.invalid')], 401);
    }
}