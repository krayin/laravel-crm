<?php

declare(strict_types=1);

namespace Stancl\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class Tenancy
{
    use Macroable;

    /** @var Tenant|Model|null */
    public $tenant;

    /** @var callable|null */
    public $getBootstrappersUsing = null;

    /** @var bool */
    public $initialized = false;

    /**
     * Initializes the tenant.
     * @param Tenant|int|string $tenant
     * @return void
     */
    public function initialize($tenant): void
    {
        if (! is_object($tenant)) {
            $tenantId = $tenant;
            $tenant = $this->find($tenantId);

            if (! $tenant) {
                throw new TenantCouldNotBeIdentifiedById($tenantId);
            }
        }

        if ($this->initialized && $this->tenant->getTenantKey() === $tenant->getTenantKey()) {
            return;
        }

        // TODO: Remove this (so that runForMultiple() is still performant) and make the FS bootstrapper work either way
        if ($this->initialized) {
            $this->end();
        }

        $this->tenant = $tenant;

        event(new Events\InitializingTenancy($this));

        $this->initialized = true;

        event(new Events\TenancyInitialized($this));
    }

    public function end(): void
    {
        event(new Events\EndingTenancy($this));

        if (! $this->initialized) {
            return;
        }

        event(new Events\TenancyEnded($this));

        $this->initialized = false;

        $this->tenant = null;
    }

    /** @return TenancyBootstrapper[] */
    public function getBootstrappers(): array
    {
        // If no callback for getting bootstrappers is set, we just return all of them.
        $resolve = $this->getBootstrappersUsing ?? function (Tenant $tenant) {
            return config('tenancy.bootstrappers');
        };

        // Here We instantiate the bootstrappers and return them.
        return array_map('app', $resolve($this->tenant));
    }

    public function query(): Builder
    {
        return $this->model()->query();
    }

    /** @return Tenant|Model */
    public function model()
    {
        $class = config('tenancy.tenant_model');

        return new $class;
    }

    public function find($id): ?Tenant
    {
        return $this->model()->where($this->model()->getTenantKeyName(), $id)->first();
    }

    /**
     * Run a callback in the central context.
     * Atomic, safely reverts to previous context.
     *
     * @param callable $callback
     * @return mixed
     */
    public function central(callable $callback)
    {
        $previousTenant = $this->tenant;

        $this->end();

        // This callback will usually not accept arguments, but the previous
        // Tenant is the only value that can be useful here, so we pass that.
        $result = $callback($previousTenant);

        if ($previousTenant) {
            $this->initialize($previousTenant);
        }

        return $result;
    }

    /**
     * Run a callback for multiple tenants.
     * More performant than running $tenant->run() one by one.
     *
     * @param Tenant[]|\Traversable|string[]|null $tenants
     * @param callable $callback
     * @return void
     */
    public function runForMultiple($tenants, callable $callback)
    {
        // Convert null to all tenants
        $tenants = is_null($tenants) ? $this->model()->cursor() : $tenants;

        // Convert incrementing int ids to strings
        $tenants = is_int($tenants) ? (string) $tenants : $tenants;

        // Wrap string in array
        $tenants = is_string($tenants) ? [$tenants] : $tenants;

        // Use all tenants if $tenants is falsey
        $tenants = $tenants ?: $this->model()->cursor();

        $originalTenant = $this->tenant;

        foreach ($tenants as $tenant) {
            if (! $tenant instanceof Tenant) {
                $tenant = $this->find($tenant);
            }

            $this->initialize($tenant);
            $callback($tenant);
        }

        if ($originalTenant) {
            $this->initialize($originalTenant);
        } else {
            $this->end();
        }
    }
}
