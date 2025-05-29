<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Bootstrappers;

use Illuminate\Support\Str;
use Illuminate\Config\Repository;
use Illuminate\Queue\QueueManager;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Support\Testing\Fakes\QueueFake;
use Illuminate\Contracts\Foundation\Application;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;

class QueueTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var Repository */
    protected $config;

    /** @var QueueManager */
    protected $queue;

    /**
     * Don't persist the same tenant across multiple jobs even if they have the same tenant ID.
     *
     * This is useful when you're changing the tenant's state (e.g. properties in the `data` column) and want the next job to initialize tenancy again
     * with the new data. Features like the Tenant Config are only executed when tenancy is initialized, so the re-initialization is needed in some cases.
     *
     * @deprecated This now has no effect, tenancy is always ended between queued jobs.
     *
     * @var bool
     */
    public static $forceRefresh = false;

    /**
     * The normal constructor is only executed after tenancy is bootstrapped.
     * However, we're registering a hook to initialize tenancy. Therefore,
     * we need to register the hook at service provider execution time.
     */
    public static function __constructStatic(Application $app)
    {
        static::setUpJobListener($app->make(Dispatcher::class));
    }

    public function __construct(Repository $config, QueueManager $queue)
    {
        $this->config = $config;
        $this->queue = $queue;

        $this->setUpPayloadGenerator();
    }

    protected static function setUpJobListener($dispatcher)
    {
        $previousTenant = null;

        $dispatcher->listen(JobProcessing::class, function ($event) use (&$previousTenant) {
            $previousTenant = tenant();

            static::initializeTenancyForQueue($event->job->payload()['tenant_id'] ?? null);
        });

        $dispatcher->listen(JobRetryRequested::class, function ($event) use (&$previousTenant) {
            $previousTenant = tenant();

            static::initializeTenancyForQueue($event->payload()['tenant_id'] ?? null);
        });

        $revertToPreviousState = function ($event) use (&$previousTenant) {
            static::revertToPreviousState($event, $previousTenant);
        };

        $dispatcher->listen(JobProcessed::class, $revertToPreviousState); // artisan('queue:work') which succeeds
        $dispatcher->listen(JobFailed::class, $revertToPreviousState); // artisan('queue:work') which fails
    }

    protected static function initializeTenancyForQueue($tenantId)
    {
        if (! $tenantId) {
            // The job is not tenant-aware, so we make sure tenancy isn't initialized.
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            return;
        }

        tenancy()->initialize(tenancy()->find($tenantId));
    }

    protected static function revertToPreviousState($event, &$previousTenant)
    {
        $tenantId = $event->job->payload()['tenant_id'] ?? null;

        if (! $tenantId) {
            // The job was not tenant-aware, so there's nothing to revert
            return;
        }

        if (tenant() && $previousTenant && $previousTenant->is(tenant())) {
            // dispatchNow() was used and the tenant in the job is the same as the previous tenant
            return;
        }

        if (tenant() && $previousTenant && $previousTenant->isNot(tenant())) {
            // Revert back to the previous tenant (since Tenancy v3.8.5 this should should *likely* not happen)
            tenancy()->initialize($previousTenant);
            return;
        }

        if (tenant() && (! $previousTenant)) {
            // No previous tenant = previous context was central
            // NOTE: Since Tenancy v3.8.5 this should *likely* not happen
            tenancy()->end();
        }
    }

    protected function setUpPayloadGenerator()
    {
        $bootstrapper = &$this;

        if (! $this->queue instanceof QueueFake) {
            $this->queue->createPayloadUsing(function ($connection) use (&$bootstrapper) {
                return $bootstrapper->getPayload($connection);
            });
        }
    }

    public function bootstrap(Tenant $tenant)
    {
        //
    }

    public function revert()
    {
        //
    }

    public function getPayload(string $connection)
    {
        if (! tenancy()->initialized) {
            return [];
        }

        if ($this->config["queue.connections.$connection.central"]) {
            return [];
        }

        $id = tenant()->getTenantKey();

        return [
            'tenant_id' => $id,
        ];
    }
}
