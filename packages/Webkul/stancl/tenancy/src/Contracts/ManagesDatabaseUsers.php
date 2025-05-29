<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Contracts;

use Stancl\Tenancy\DatabaseConfig;

interface ManagesDatabaseUsers extends TenantDatabaseManager
{
    public function createUser(DatabaseConfig $databaseConfig): bool;

    public function deleteUser(DatabaseConfig $databaseConfig): bool;

    public function userExists(string $username): bool;
}
