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
             * completion state is recorded only when the installation finishes, so an installation in
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
     * Unlike isAlreadyInstalled(), this relies solely on completion state recorded at the end of the
     * web and console installer, so it is never true while an installation is still in progress.
     */
    public function isInstallationComplete(): bool
    {
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        /**
         * The marker file is gitignored, so a fresh checkout, a container rebuild or any deploy that
         * does not carry `storage/` across arrives without it while the database is still fully
         * populated. Falling back to the database flag keeps the installer closed in that case
         * instead of reopening it to anyone on a live application.
         */
        return app(DatabaseManager::class)->isInstallationCompleted();
    }

    /**
     * Check if application is already installed.
     */
    public function isAlreadyInstalled(): bool
    {
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        if (($databaseManager = app(DatabaseManager::class))->isInstalled()) {
            touch(storage_path('installed'));

            /**
             * Backfill for an application installed before completion was recorded in the database.
             * Reaching here means the marker file was missing while the database was fully
             * installed, so the flag is written once and then travels with the data, keeping the
             * installer closed on every later deploy that arrives without `storage/`.
             */
            $databaseManager->markInstallationCompleted();

            Event::dispatch('krayin.installed');

            return true;
        }

        return false;
    }
}
