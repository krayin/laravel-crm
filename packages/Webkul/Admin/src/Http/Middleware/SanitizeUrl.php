<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Webkul\Email\Enums\SupportedFolderEnum;

class SanitizeUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }

        $route = $request->route('route');

        $sanitizedRoute = Str::of($route)->ascii()->lower()->replaceMatches('/[^a-z0-9_-]/', '')->__toString();

        $request->route()->setParameter('route', $sanitizedRoute);

        /**
         * Whitelist acceptable route values to prevent unexpected input
         */
        $allowedRoutes = [
            SupportedFolderEnum::INBOX->value,
            SupportedFolderEnum::IMPORTANT->value,
            SupportedFolderEnum::STARRED->value,
            SupportedFolderEnum::DRAFT->value,
            SupportedFolderEnum::OUTBOX->value,
            SupportedFolderEnum::SENT->value,
            SupportedFolderEnum::SPAM->value,
            SupportedFolderEnum::TRASH->value,
        ];

        if (! in_array($route, $allowedRoutes, true)) {
            abort(401, trans('admin::app.mail.invalid-route'));
        }

        return $next($request);
    }
}
