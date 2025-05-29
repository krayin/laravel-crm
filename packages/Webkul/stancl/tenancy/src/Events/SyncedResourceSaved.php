<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Events;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts\Syncable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class SyncedResourceSaved
{
    /** @var Syncable|Model */
    public $model;

    /** @var TenantWithDatabase|Model|null */
    public $tenant;

    public function __construct(Syncable $model, ?TenantWithDatabase $tenant)
    {
        $this->model = $model;
        $this->tenant = $tenant;
    }
}
