<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Database\Concerns;
use Stancl\Tenancy\Events;

/**
 * @property string $domain
 * @property string $tenant_id
 *
 * @property-read Tenant|Model $tenant
 */
class Domain extends Model implements Contracts\Domain
{
    use Concerns\CentralConnection,
        Concerns\EnsuresDomainIsNotOccupied,
        Concerns\ConvertsDomainsToLowercase,
        Concerns\InvalidatesTenantsResolverCache;

    protected $guarded = [];

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'));
    }

    protected $dispatchesEvents = [
        'saving' => Events\SavingDomain::class,
        'saved' => Events\DomainSaved::class,
        'creating' => Events\CreatingDomain::class,
        'created' => Events\DomainCreated::class,
        'updating' => Events\UpdatingDomain::class,
        'updated' => Events\DomainUpdated::class,
        'deleting' => Events\DeletingDomain::class,
        'deleted' => Events\DomainDeleted::class,
    ];
}
