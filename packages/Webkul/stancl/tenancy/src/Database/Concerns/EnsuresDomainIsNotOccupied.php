<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\Tenancy\Exceptions\DomainOccupiedByOtherTenantException;

trait EnsuresDomainIsNotOccupied
{
    public static function bootEnsuresDomainIsNotOccupied()
    {
        static::saving(function ($self) {
            if ($domain = $self->newQuery()->where('domain', $self->domain)->first()) {
                if ($domain->getKey() !== $self->getKey()) {
                    throw new DomainOccupiedByOtherTenantException($self->domain);
                }
            }
        });
    }
}
