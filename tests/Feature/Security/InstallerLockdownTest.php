<?php

use Illuminate\Http\Request;
use Webkul\Installer\Helpers\DatabaseManager;
use Webkul\Installer\Http\Middleware\CanInstall;

/**
 * The installer API endpoints overwrite the administrator account and re-run `migrate:fresh`
 * against live data, so they must be unreachable once the application is installed.
 *
 * The guard used to rest solely on `storage/installed`. That file is gitignored, so a fresh
 * checkout, a container rebuild, or any deploy that does not carry `storage/` across arrives
 * without it while the database is still fully populated — which reopened the endpoints to
 * anonymous callers.
 *
 * These tests point the application at a throwaway storage path, so the real marker file is never
 * created, moved or deleted, and stub the database so no query runs.
 */
beforeEach(function () {
    $this->originalStoragePath = app()->storagePath();

    $this->tempStorage = sys_get_temp_dir().'/krayin-installer-test-'.bin2hex(random_bytes(6));

    mkdir($this->tempStorage, 0777, true);

    app()->useStoragePath($this->tempStorage);

    /**
     * $completed  — whether the completion flag has been recorded in the database.
     * $populated  — whether the database actually holds an installed application.
     */
    $this->fakeDatabase = function (bool $completed, bool $populated = false) {
        app()->instance(DatabaseManager::class, new class($completed, $populated) extends DatabaseManager
        {
            public bool $backfilled = false;

            public function __construct(private bool $completed, private bool $populated) {}

            public function isInstallationCompleted(): bool
            {
                return $this->completed;
            }

            public function isInstalled()
            {
                return $this->populated;
            }

            public function markInstallationCompleted(): void
            {
                $this->backfilled = true;
                $this->completed = true;
            }
        });
    };
});

afterEach(function () {
    app()->useStoragePath($this->originalStoragePath);

    @unlink($this->tempStorage.'/installed');
    @unlink($this->tempStorage.'/installing');
    @rmdir($this->tempStorage);
});

it('treats the application as installed when the marker file is present', function () {
    ($this->fakeDatabase)(false);

    touch(storage_path('installed'));

    expect((new CanInstall)->isInstallationComplete())->toBeTrue();
});

it('treats the application as installed when the marker is gone but the database says so', function () {
    ($this->fakeDatabase)(true);

    expect(file_exists(storage_path('installed')))->toBeFalse()
        ->and((new CanInstall)->isInstallationComplete())->toBeTrue();
});

it('still allows a genuine installation to proceed', function () {
    ($this->fakeDatabase)(false);

    expect(file_exists(storage_path('installed')))->toBeFalse()
        ->and((new CanInstall)->isInstallationComplete())->toBeFalse();
});

it('blocks the installer api on an installed app that lost its marker file', function (string $path) {
    ($this->fakeDatabase)(true);

    $reached = false;

    $response = (new CanInstall)->handle(
        Request::create($path, 'POST'),
        function () use (&$reached) {
            $reached = true;

            return response('controller reached');
        }
    );

    expect($reached)->toBeFalse()
        ->and($response->getStatusCode())->toBe(302);
})->with([
    '/install/api/admin-config-setup',
    '/install/api/run-migration',
    '/install/api/run-seeder',
    '/install/api/env-file-setup',
]);

it('lets the installer api through while an installation is genuinely in progress', function () {
    ($this->fakeDatabase)(false);

    $reached = false;

    (new CanInstall)->handle(
        Request::create('/install/api/run-migration', 'POST'),
        function () use (&$reached) {
            $reached = true;

            return response('ok');
        }
    );

    expect($reached)->toBeTrue();
});

/**
 * A legacy installation — one completed before the database flag existed — has neither signal
 * after a deploy that leaves `storage/` behind, yet its database is fully populated. The installer
 * API must still be closed to it: reaching run-migration there wipes live data, and
 * admin-config-setup overwrites the administrator account.
 */
it('treats a legacy installation with no flag and no marker as installed', function () {
    ($this->fakeDatabase)(completed: false, populated: true);

    expect(file_exists(storage_path('installed')))->toBeFalse()
        ->and((new CanInstall)->isInstallationComplete())->toBeTrue();
});

it('blocks the installer api for a legacy installation that lost its marker file', function (string $path) {
    ($this->fakeDatabase)(completed: false, populated: true);

    $reached = false;

    (new CanInstall)->handle(Request::create($path, 'POST'), function () use (&$reached) {
        $reached = true;

        return response('controller reached');
    });

    expect($reached)->toBeFalse();
})->with([
    '/install/api/run-migration',
    '/install/api/admin-config-setup',
    '/install/api/env-file-setup',
]);

/**
 * The seeder creates the default administrator before `admin-config-setup` runs, so from that
 * moment the database looks "installed" while the installation is in fact still going. The
 * in-progress marker is what keeps the final step reachable.
 */
it('does not lock out the final installer step once the seeder has run', function () {
    ($this->fakeDatabase)(completed: false, populated: true);

    app(DatabaseManager::class)->markInstallationInProgress();

    expect(app(DatabaseManager::class)->isInstallationComplete())->toBeFalse();

    $reached = false;

    (new CanInstall)->handle(Request::create('/install/api/admin-config-setup', 'POST'), function () use (&$reached) {
        $reached = true;

        return response('ok');
    });

    expect($reached)->toBeTrue();
});

it('closes the installer again once the in-progress marker is cleared', function () {
    ($this->fakeDatabase)(completed: false, populated: true);

    $database = app(DatabaseManager::class);

    $database->markInstallationInProgress();
    expect($database->isInstallationComplete())->toBeFalse();

    $database->clearInstallationInProgress();
    expect($database->isInstallationComplete())->toBeTrue();
});

it('backfills the database flag when it infers completion from the database', function () {
    ($this->fakeDatabase)(completed: false, populated: true);

    $database = app(DatabaseManager::class);

    expect($database->backfilled)->toBeFalse();

    $database->isInstallationComplete();

    expect($database->backfilled)->toBeTrue();
});
