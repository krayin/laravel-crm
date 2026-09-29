<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Menu\MenuItem;

class SessionController extends Controller
{
    /**
     * Number of failed attempts allowed per minute, per set of credentials and per address.
     */
    private const MAX_ATTEMPTS_PER_CREDENTIAL = 5;

    private const MAX_ATTEMPTS_PER_ADDRESS = 20;

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse|View
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        $previousUrl = url()->previous();

        $intendedUrl = str_contains($previousUrl, 'admin')
            ? $previousUrl
            : route('admin.dashboard.index');

        session()->put('url.intended', $intendedUrl);

        return view('admin::sessions.login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->ensureIsNotRateLimited();

        if (! auth()->guard('user')->attempt(request(['email', 'password']), request('remember'))) {
            $this->recordFailedAttempt();

            session()->flash('error', trans('admin::app.users.login-error'));

            return redirect()->back();
        }

        $this->clearRateLimit();

        if (auth()->guard('user')->user()->status == 0) {
            session()->flash('warning', trans('admin::app.users.activate-warning'));

            auth()->guard('user')->logout();

            return redirect()->route('admin.session.create');
        }

        $menus = menu()->getItems('admin');

        $availableNextMenu = $menus?->first();

        if (! bouncer()->hasPermission('dashboard')) {
            if (is_null($availableNextMenu)) {
                session()->flash('error', trans('admin::app.users.not-permission'));

                auth()->guard('user')->logout();

                return redirect()->route('admin.session.create');
            }

            return redirect()->to($availableNextMenu->getUrl());
        }

        $hasAccessToIntendedUrl = $this->canAccessIntendedUrl($menus, redirect()->getIntendedUrl());

        if ($hasAccessToIntendedUrl) {
            return redirect()->intended(route('admin.dashboard.index'));
        }

        return redirect()->to($availableNextMenu->getUrl());
    }

    /**
     * Stop the request when too many recent attempts have already failed.
     *
     * Only failures are counted. Throttling every attempt would also throttle people who sign in
     * correctly — a browser test suite signing in for each of its cases, or several staff behind one
     * office address — while doing nothing extra against password guessing, which is made up
     * entirely of failures.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (
            RateLimiter::tooManyAttempts($this->credentialRateLimitKey(), self::MAX_ATTEMPTS_PER_CREDENTIAL)
            || RateLimiter::tooManyAttempts($this->addressRateLimitKey(), self::MAX_ATTEMPTS_PER_ADDRESS)
        ) {
            abort(429);
        }
    }

    /**
     * Count a failed attempt against both the credentials used and the address it came from, so
     * neither guessing one password nor spraying many accounts from one place goes unchecked.
     */
    protected function recordFailedAttempt(): void
    {
        RateLimiter::hit($this->credentialRateLimitKey());

        RateLimiter::hit($this->addressRateLimitKey());
    }

    /**
     * A successful sign-in clears the record for those credentials.
     */
    protected function clearRateLimit(): void
    {
        RateLimiter::clear($this->credentialRateLimitKey());
    }

    protected function credentialRateLimitKey(): string
    {
        return 'admin-login|'.sha1(Str::lower((string) request('email')).'|'.request()->ip());
    }

    protected function addressRateLimitKey(): string
    {
        return 'admin-login-address|'.sha1((string) request()->ip());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): RedirectResponse
    {
        auth()->guard('user')->logout();

        /**
         * Drop every value the session carried and rotate its identifier, so no
         * data survives the logout and the id cannot be replayed afterwards.
         */
        session()->invalidate();

        session()->regenerateToken();

        return redirect()->route('admin.session.create');
    }

    /**
     * Find menu item by URL.
     */
    protected function canAccessIntendedUrl(Collection $menus, ?string $url): ?MenuItem
    {
        if (is_null($url)) {
            return null;
        }

        foreach ($menus as $menu) {
            if ($menu->getUrl() === $url) {
                return $menu;
            }

            if ($menu->haveChildren()) {
                $found = $this->canAccessIntendedUrl($menu->getChildren(), $url);

                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }
}
