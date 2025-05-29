<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Events;

use Stancl\Tenancy\Contracts\Syncable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class SyncedResourceChangedInForeignDatabase
{
    /** @var Syncable */
    public $model;

    /** @var TenantWithDatabase|null */
    public $tenant;

    public function __construct(Syncable $model, ?TenantWithDatabase $tenant)
    {
        $this->model = $model;
        $this->tenant = $tenant;
    }
}
