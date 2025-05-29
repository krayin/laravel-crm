<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database;

use Illuminate\Database\Eloquent\Collection;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * @property Tenant[] $items
 * @method void __construct(Tenant[] $items = [])
 * @method Tenant[] toArray()
 * @method Tenant offsetGet($key)
 * @method Tenant first()
 */
class TenantCollection extends Collection
{
    public function runForEach(callable $callable): self
    {
        tenancy()->runForMultiple($this->items, $callable);

        return $this;
    }
}
