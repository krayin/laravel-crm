<?php

declare(strict_types=1);

namespace Stancl\Tenancy\TenantDatabaseManagers;

use Stancl\Tenancy\Concerns\CreatesDatabaseUsers;
use Stancl\Tenancy\Contracts\ManagesDatabaseUsers;
use Stancl\Tenancy\DatabaseConfig;

class PermissionControlledMySQLDatabaseManager extends MySQLDatabaseManager implements ManagesDatabaseUsers
{
    use CreatesDatabaseUsers;

    public static $grants = [
        'ALTER', 'ALTER ROUTINE', 'CREATE', 'CREATE ROUTINE', 'CREATE TEMPORARY TABLES', 'CREATE VIEW',
        'DELETE', 'DROP', 'EVENT', 'EXECUTE', 'INDEX', 'INSERT', 'LOCK TABLES', 'REFERENCES', 'SELECT',
        'SHOW VIEW', 'TRIGGER', 'UPDATE',
    ];

    public function createUser(DatabaseConfig $databaseConfig): bool
    {
        $database = $databaseConfig->getName();
        $username = $databaseConfig->getUsername();
        $hostname = $databaseConfig->connection()['host'];
        $password = $databaseConfig->getPassword();

        $this->database()->statement("CREATE USER `{$username}`@`%` IDENTIFIED BY '{$password}'");

        $grants = implode(', ', static::$grants);

        if ($this->isVersion8()) { // MySQL 8+
            $grantQuery = "GRANT $grants ON `$database`.* TO `$username`@`%`";
        } else { // MySQL 5.7
            $grantQuery = "GRANT $grants ON `$database`.* TO `$username`@`%` IDENTIFIED BY '$password'";
        }

        return $this->database()->statement($grantQuery);
    }

    protected function isVersion8(): bool
    {
        $versionSelect = $this->database()->raw('select version()')->getValue($this->database()->getQueryGrammar());
        $version = $this->database()->select($versionSelect)[0]->{'version()'};

        return version_compare($version, '8.0.0') >= 0;
    }

    public function deleteUser(DatabaseConfig $databaseConfig): bool
    {
        return $this->database()->statement("DROP USER IF EXISTS '{$databaseConfig->getUsername()}'");
    }

    public function userExists(string $username): bool
    {
        return (bool) $this->database()->select("SELECT count(*) FROM mysql.user WHERE user = '$username'")[0]->{'count(*)'};
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        $baseConfig['database'] = $databaseName;

        return $baseConfig;
    }
}
