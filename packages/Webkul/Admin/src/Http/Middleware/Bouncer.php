<?php

namespace Webkul\Admin\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Bouncer
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, \Closure $next, $guard = 'user')
    {
        if (! auth()->guard($guard)->check()) {
            return redirect()->route('admin.session.create');
        }

        /**
         * If user status is changed by admin. Then session should be
         * logged out.
         */
        if (! (bool) auth()->guard($guard)->user()->status) {
            $this->terminateSession($guard);

            return redirect()->route('admin.session.create');
        }

        /**
         * If somehow the user deleted all permissions, then it should be
         * auto logged out and need to contact the administrator again.
         */
        if ($this->isPermissionsEmpty()) {
            $this->terminateSession($guard);

            return redirect()->route('admin.session.create');
        }

        return $next($request);
    }

    /**
     * Log the user out and tear the session down, then flash the reason.
     *
     * The session is invalidated and its token rotated so nothing the revoked
     * session held survives; the message is flashed afterwards because
     * invalidating would otherwise discard it.
     */
    protected function terminateSession($guard): void
    {
        auth()->guard($guard)->logout();

        session()->invalidate();

        session()->regenerateToken();

        session()->flash('error', trans('admin::app.errors.401'));
    }

    /**
     * Check for user, if they have empty permissions or not except admin.
     *
     * @return bool
     */
    public function isPermissionsEmpty()
    {
        if (! $role = auth()->guard('user')->user()->role) {
            abort(401, 'This action is unauthorized.');
        }

        if ($role->permission_type === 'all') {
            return false;
        }

        if ($role->permission_type !== 'all' && empty($role->permissions)) {
            return true;
        }

        $this->checkIfAuthorized();

        return false;
    }

    /**
     * Check authorization.
     *
     * The middleware fails closed: an administrative route that is not covered by the ACL map is
     * denied rather than allowed. A route without its own mapping inherits the permission of the
     * nearest mapped ancestor feature (so, for example, an unmapped `admin.leads.search` still
     * requires the `leads` permission), while a small allow-list covers authentication, self-service
     * account management, and generic UI helpers that expose no privileged data.
     *
     * @return void
     */
    public function checkIfAuthorized()
    {
        $routeName = Route::currentRouteName();

        if (! $routeName) {
            return;
        }

        $roles = acl()->getRoles();

        /**
         * Exact ACL mapping.
         */
        if (isset($roles[$routeName])) {
            bouncer()->allow($roles[$routeName]);

            return;
        }

        /**
         * Public and self-service routes that any authenticated user may reach.
         */
        if ($this->isAllowlistedRoute($routeName)) {
            return;
        }

        /**
         * Fail closed: authorize against the nearest mapped ancestor feature, otherwise deny.
         */
        if (! $this->isAuthorizedByFeature($routeName, $roles)) {
            abort(401, 'This action is unauthorized.');
        }
    }

    /**
     * Routes every authenticated user may reach regardless of their role — authentication,
     * self-service account management, and generic UI helpers exposing no privileged data.
     */
    protected function isAllowlistedRoute(string $routeName): bool
    {
        return in_array($routeName, [
            'admin.session.create',
            'admin.session.store',
            'admin.session.destroy',
            'admin.forgot_password.create',
            'admin.forgot_password.store',
            'admin.reset_password.create',
            'admin.reset_password.store',
            'admin.user.account.edit',
            'admin.user.account.update',
            'admin.datagrid.look_up',
            'admin.datagrid.saved_filters.index',
            'admin.datagrid.saved_filters.store',
            'admin.datagrid.saved_filters.update',
            'admin.datagrid.saved_filters.destroy',
            'admin.tinymce.upload',
            'admin.settings.web_forms.form_js',
            'admin.settings.web_forms.form_store',
            'admin.settings.web_forms.preview',
            'admin.mail.inbound_parse',
        ], true);
    }

    /**
     * Authorize an unmapped route against the permission of its nearest mapped ancestor feature.
     * Walks up the dotted route name until a mapped feature is found, then requires the user to hold
     * at least one permission within that feature.
     */
    protected function isAuthorizedByFeature(string $routeName, $roles): bool
    {
        $segments = explode('.', $routeName);

        while (count($segments) > 2) {
            array_pop($segments);

            $prefix = implode('.', $segments).'.';

            $featureKeys = collect($roles)
                ->filter(fn ($key, $route) => str_starts_with($route, $prefix))
                ->values();

            if ($featureKeys->isEmpty()) {
                continue;
            }

            $user = auth()->guard('user')->user();

            return $featureKeys->contains(fn ($key) => $user->hasPermission($key));
        }

        return false;
    }
}
