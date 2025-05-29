<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Features;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Contracts\Feature;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Database\Models\ImpersonationToken;
use Stancl\Tenancy\Tenancy;

class UserImpersonation implements Feature
{
    public static $ttl = 60; // seconds

    public function bootstrap(Tenancy $tenancy): void
    {
        $tenancy->macro('impersonate', function (Tenant $tenant, string $userId, string $redirectUrl, ?string $authGuard = null): ImpersonationToken {
            return ImpersonationToken::create([
                'tenant_id' => $tenant->getTenantKey(),
                'user_id' => $userId,
                'redirect_url' => $redirectUrl,
                'auth_guard' => $authGuard,
            ]);
        });
    }

    /**
     * Impersonate a user and get an HTTP redirect response.
     *
     * @param string|ImpersonationToken $token
     * @param int|null $ttl
     * @return RedirectResponse
     */
    public static function makeResponse($token, ?int $ttl = null): RedirectResponse
    {
        $token = $token instanceof ImpersonationToken ? $token : ImpersonationToken::findOrFail($token);

        if (((string) $token->tenant_id) !== ((string) tenant()->getTenantKey())) {
            abort(403);
        }

        $ttl = $ttl ?? static::$ttl;

        if ($token->created_at->diffInSeconds(Carbon::now()) > $ttl) {
            abort(403);
        }

        Auth::guard($token->auth_guard)->loginUsingId($token->user_id);

        $token->delete();

        return redirect($token->redirect_url);
    }
}
