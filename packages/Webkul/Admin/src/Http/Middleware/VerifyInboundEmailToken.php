<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Authenticates the inbound parse webhook.
 *
 * The route drops the `user` middleware and is exempt from CSRF, because the request comes
 * from the mail provider rather than from a signed-in admin. Without a check of its own the
 * endpoint accepts a raw message from anyone who can reach the host, and that message is
 * stored as a genuine email: any sender, any subject, any body.
 *
 * Providers that POST here let you choose the URL, so a shared secret can travel with the
 * request. SendGrid's Inbound Parse accepts credentials embedded in the destination URL;
 * other providers offer a custom header. Both are supported below.
 */
class VerifyInboundEmailToken
{
    /**
     * The header a provider can be configured to send.
     */
    const HEADER = 'X-Inbound-Token';

    /**
     * Rejections logged per minute, per client. The endpoint is unauthenticated by
     * design, so one log line per attempt is a way to fill the disk.
     */
    const LOG_PER_MINUTE = 5;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $expected = config('mail-receiver.inbound_token');

        if (blank($expected)) {
            $this->log($request, 'Rejected an inbound parse request because MAIL_RECEIVER_INBOUND_TOKEN is not set.');

            /**
             * 503, not 403. Providers retry a 5xx and drop the message on a 4xx, so an
             * installation that upgrades before setting the token holds its mail in the
             * provider's retry queue instead of losing it.
             */
            return response()->json([
                'message' => 'Inbound parsing is not configured.',
            ], 503);
        }

        if (! $this->hasValidToken($request, $expected)) {
            $this->log($request, 'Rejected an inbound parse request carrying an invalid token.');

            return response()->json([
                'message' => 'Invalid inbound token.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * The token may arrive as a header or as HTTP basic auth, depending on what the
     * provider allows. Compared with hash_equals so the check does not leak the secret
     * through timing.
     */
    protected function hasValidToken(Request $request, string $expected): bool
    {
        foreach ([$request->header(self::HEADER), $request->getPassword()] as $provided) {
            if (is_string($provided) && hash_equals($expected, $provided)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log the rejection, but at most a few times a minute per client.
     */
    protected function log(Request $request, string $message): void
    {
        $key = 'inbound-parse-rejected:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::LOG_PER_MINUTE)) {
            return;
        }

        RateLimiter::hit($key, 60);

        Log::warning($message, ['ip' => $request->ip()]);
    }
}
