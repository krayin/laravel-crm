<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Contracts;

use Stancl\Tenancy\Exceptions\NoConnectionSetException;

interface TenantDatabaseManager
{
    /**
     * Create a database.
     */
    public function createDatabase(TenantWithDatabase $tenant): bool;

    /**
     * Delete a database.
     */
    public function deleteDatabase(TenantWithDatabase $tenant): bool;

    /**
     * Does a database exist.
     */
    public function databaseExists(string $name): bool;

    /**
     * Make a DB connection config array.
     */
    public function makeConnectionConfig(array $baseConfig, string $databaseName): array;

    /**
     * Set the DB connection that should be used by the tenant database manager.
     *
     * @throws NoConnectionSetException
     */
    public function setConnection(string $connection): void;
}
