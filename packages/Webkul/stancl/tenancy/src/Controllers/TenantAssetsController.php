<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Throwable;

class TenantAssetsController extends Controller
{
    public static $tenancyMiddleware = 'Stancl\Tenancy\Middleware\InitializeTenancyByDomain';

    public function __construct()
    {
        $this->middleware(static::$tenancyMiddleware);
    }

    public function asset($path = null)
    {
        $this->validatePath($path);

        try {
            return response()->file(storage_path("app/public/$path"));
        } catch (Throwable $th) {
            abort(404);
        }
    }

    /**
     * Prevent path traversal attacks. This is generally a non-issue on modern
     * webservers but it's still worth handling on the application level as well.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validatePath(string|null $path): void
    {
        $this->abortIf($path === null, 'Empty path');

        $allowedRoot = realpath(storage_path('app/public'));

        // `storage_path('app/public')` doesn't exist, so it cannot contain files
        $this->abortIf($allowedRoot === false, "Storage root doesn't exist");

        $attemptedPath = realpath("{$allowedRoot}/{$path}");

        // User is attempting to access a nonexistent file
        $this->abortIf($attemptedPath === false, 'Accessing a nonexistent file');

        // User is attempting to access a file outside the $allowedRoot folder
        $this->abortIf(! str($attemptedPath)->startsWith($allowedRoot), 'Accessing a file outside the storage root');
    }

    protected function abortIf($condition, $exceptionMessage): void
    {
        if ($condition) {
            if (app()->runningUnitTests()) {
                // Makes testing the cause of the failure in validatePath() easier
                throw new Exception($exceptionMessage);
            } else {
                // We always use 404 to avoid leaking information about the cause of the error
                // e.g. when someone is trying to access a nonexistent file outside of the allowed
                // root folder, we don't want to let the user know whether such a file exists or not.
                abort(404);
            }
        }
    }
}
