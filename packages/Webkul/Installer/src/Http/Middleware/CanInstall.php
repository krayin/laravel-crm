<?php

namespace Webkul\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Webkul\Installer\Helpers\DatabaseManager;

class CanInstall
{
    /**
     * Handles Requests if application is already installed then redirect to dashboard else to installer.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Str::contains($request->getPathInfo(), '/install')) {
            /**
             * Once the application is fully installed, the installer and its API endpoints must be
             * unreachable. Previously AJAX requests were exempted here, but whether a request is
             * "AJAX" is decided solely by the client supplied `X-Requested-With` header, which let an
             * unauthenticated request reach the installer endpoints on a live application. The
             * completion marker is written only when the installation finishes, so an installation in
             * progress is unaffected.
             */
            if ($this->isInstallationComplete()) {
                if ($request->ajax()) {
                    abort(403);
                }

                return redirect()->route('admin.dashboard.index');
            }
        } elseif (! $this->isAlreadyInstalled()) {
            return redirect()->route('installer.index');
        }

        return $next($request);
    }

    /**
     * Whether the installation has fully completed.
     *
     * Unlike isAlreadyInstalled(), this relies solely on the completion marker written at the end of
     * the web and console installer, so it is never true while an installation is still in progress.
     */
    public function isInstallationComplete(): bool
    {
        return file_exists(storage_path('installed'));
    }

    /**
     * Check if application is already installed.
     */
    public function isAlreadyInstalled(): bool
    {
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        if (app(DatabaseManager::class)->isInstalled()) {
            touch(storage_path('installed'));

            Event::dispatch('krayin.installed');

            return true;
        }

        return false;
    }
}
