<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Concerns;

use Stancl\Tenancy\Contracts\TenantWithDatabase;

trait CreatesDatabaseUsers
{
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        parent::createDatabase($tenant);

        return $this->createUser($tenant->database());
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        parent::deleteDatabase($tenant);

        return $this->deleteUser($tenant->database());
    }
}
