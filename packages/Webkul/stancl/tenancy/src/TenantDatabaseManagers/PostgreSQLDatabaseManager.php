<?php

declare(strict_types=1);

namespace Stancl\Tenancy\TenantDatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\NoConnectionSetException;

class PostgreSQLDatabaseManager implements TenantDatabaseManager
{
    /** @var string */
    protected $connection;

    protected function database(): Connection
    {
        if ($this->connection === null) {
            throw new NoConnectionSetException(static::class);
        }

        return DB::connection($this->connection);
    }

    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        return $this->database()->statement("CREATE DATABASE \"{$tenant->database()->getName()}\" WITH TEMPLATE=template0");
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        return $this->database()->statement("DROP DATABASE \"{$tenant->database()->getName()}\"");
    }

    public function databaseExists(string $name): bool
    {
        return (bool) $this->database()->select("SELECT datname FROM pg_database WHERE datname = '$name'");
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        $baseConfig['database'] = $databaseName;

        return $baseConfig;
    }
}
