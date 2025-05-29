<?php

declare(strict_types=1);

namespace Stancl\Tenancy\TenantDatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\NoConnectionSetException;

class PostgreSQLSchemaManager implements TenantDatabaseManager
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
        return $this->database()->statement("CREATE SCHEMA \"{$tenant->database()->getName()}\"");
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        return $this->database()->statement("DROP SCHEMA \"{$tenant->database()->getName()}\" CASCADE");
    }

    public function databaseExists(string $name): bool
    {
        return (bool) $this->database()->select("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$name'");
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        $baseConfig['search_path'] = $databaseName;

        return $baseConfig;
    }
}
