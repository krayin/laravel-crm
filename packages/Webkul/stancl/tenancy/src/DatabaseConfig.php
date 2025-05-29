<?php

declare(strict_types=1);

namespace Stancl\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stancl\Tenancy\Contracts\ManagesDatabaseUsers;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase as Tenant;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;

class DatabaseConfig
{
    /** @var Tenant|Model */
    public $tenant;

    /** @var callable */
    public static $usernameGenerator;

    /** @var callable */
    public static $passwordGenerator;

    /** @var callable */
    public static $databaseNameGenerator;

    public static function __constructStatic(): void
    {
        static::$usernameGenerator = static::$usernameGenerator ?? function (Tenant $tenant) {
            return Str::random(16);
        };

        static::$passwordGenerator = static::$passwordGenerator ?? function (Tenant $tenant) {
            return Hash::make(Str::random(32));
        };

        static::$databaseNameGenerator = static::$databaseNameGenerator ?? function (Tenant $tenant) {
            return config('tenancy.database.prefix') . $tenant->getTenantKey() . config('tenancy.database.suffix');
        };
    }

    public function __construct(Tenant $tenant)
    {
        static::__constructStatic();

        $this->tenant = $tenant;
    }

    public static function generateDatabaseNamesUsing(callable $databaseNameGenerator): void
    {
        static::$databaseNameGenerator = $databaseNameGenerator;
    }

    public static function generateUsernamesUsing(callable $usernameGenerator): void
    {
        static::$usernameGenerator = $usernameGenerator;
    }

    public static function generatePasswordsUsing(callable $passwordGenerator): void
    {
        static::$passwordGenerator = $passwordGenerator;
    }

    public function getName(): ?string
    {
        return $this->tenant->getInternal('db_name') ?? (static::$databaseNameGenerator)($this->tenant);
    }

    public function getUsername(): ?string
    {
        return $this->tenant->getInternal('db_username') ?? null;
    }

    public function getPassword(): ?string
    {
        return $this->tenant->getInternal('db_password') ?? null;
    }

    /**
     * Generate DB name, username & password and write them to the tenant model.
     *
     * @return void
     */
    public function makeCredentials(): void
    {
        $this->tenant->setInternal('db_name', $this->getName() ?? (static::$databaseNameGenerator)($this->tenant));

        if ($this->manager() instanceof ManagesDatabaseUsers) {
            $this->tenant->setInternal('db_username', $this->getUsername() ?? (static::$usernameGenerator)($this->tenant));
            $this->tenant->setInternal('db_password', $this->getPassword() ?? (static::$passwordGenerator)($this->tenant));
        }

        if ($this->tenant->exists) {
            $this->tenant->save();
        }
    }

    public function getTemplateConnectionName(): string
    {
        return $this->tenant->getInternal('db_connection')
            ?? config('tenancy.database.template_tenant_connection')
            ?? config('tenancy.database.central_connection');
    }

    /**
     * Tenant's own database connection config.
     */
    public function connection(): array
    {
        $template = $this->getTemplateConnectionName();
        $templateConnection = config("database.connections.{$template}");

        return $this->manager()->makeConnectionConfig(
            array_merge($templateConnection, $this->tenantConfig()), $this->getName()
        );
    }

    /**
     * Additional config for the database connection, specific to this tenant.
     */
    public function tenantConfig(): array
    {
        $dbConfig = array_filter(array_keys($this->tenant->getAttributes()), function ($key) {
            return Str::startsWith($key, $this->tenant->internalPrefix() . 'db_');
        });

        // Remove DB name because we set that separately
        if (($pos = array_search($this->tenant->internalPrefix() . 'db_name', $dbConfig)) !== false) {
            unset($dbConfig[$pos]);
        }

        // Remove DB connection because that's not used inside the array
        if (($pos = array_search($this->tenant->internalPrefix() . 'db_connection', $dbConfig)) !== false) {
            unset($dbConfig[$pos]);
        }

        return array_reduce($dbConfig, function ($config, $key) {
            return array_merge($config, [
                Str::substr($key, strlen($this->tenant->internalPrefix() . 'db_')) => $this->tenant->getAttribute($key),
            ]);
        }, []);
    }

    /**
     * Get the TenantDatabaseManager for this tenant's connection.
     */
    public function manager(): TenantDatabaseManager
    {
        $driver = config("database.connections.{$this->getTemplateConnectionName()}.driver");

        $databaseManagers = config('tenancy.database.managers');

        if (! array_key_exists($driver, $databaseManagers)) {
            throw new DatabaseManagerNotRegisteredException($driver);
        }

        /** @var TenantDatabaseManager $databaseManager */
        $databaseManager = app($databaseManagers[$driver]);

        $databaseManager->setConnection($this->getTemplateConnectionName());

        return $databaseManager;
    }
}
