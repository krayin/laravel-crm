<?php

namespace Webkul\Installer\Helpers;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Installer\Database\Seeders\DatabaseSeeder as KrayinDatabaseSeeder;

class DatabaseManager
{
    /**
     * The core_config code recording that installation finished.
     */
    public const INSTALLATION_COMPLETED_FLAG = 'installation.completed';

    /**
     * Whether the database says this application has finished installing.
     *
     * This is the durable counterpart to the `storage/installed` marker file: the file is gitignored
     * and so is lost by any deploy that does not carry `storage/` across, whereas this flag travels
     * with the data it describes. It is written only when installation completes, so it stays false
     * for the whole of a genuine installation and never blocks it.
     */
    public function isInstallationCompleted(): bool
    {
        try {
            if (! Schema::hasTable('core_config')) {
                return false;
            }

            return DB::table('core_config')
                ->where('code', self::INSTALLATION_COMPLETED_FLAG)
                ->exists();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Record that installation has finished, so the installer cannot be reopened by a deploy that
     * loses the marker file.
     */
    public function markInstallationCompleted(): void
    {
        try {
            if (! Schema::hasTable('core_config')) {
                return;
            }

            DB::table('core_config')->updateOrInsert(
                ['code' => self::INSTALLATION_COMPLETED_FLAG],
                [
                    'value' => '1',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Check Database Connection.
     */
    public function isInstalled()
    {
        if (! file_exists(base_path('.env'))) {
            return false;
        }

        try {
            DB::connection()->getPDO();

            $isConnected = (bool) DB::connection()->getDatabaseName();

            if (! $isConnected) {
                return false;
            }

            $hasUserTable = Schema::hasTable('users');

            if (! $hasUserTable) {
                return false;
            }

            $userCount = DB::table('users')->count();

            if (! $userCount) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Drop all the tables and migrate in the database
     *
     * @return void|string
     */
    public function migration()
    {
        try {
            Artisan::call('migrate:fresh');

            return response()->json([
                'success' => true,
                'message' => 'Tables is migrated successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Seed the database.
     *
     * @return void|string
     */
    public function seeder($data)
    {
        try {
            app(KrayinDatabaseSeeder::class)->run([
                'default_locale' => $data['parameter']['default_locales'],
                'default_currency' => $data['parameter']['default_currency'],
            ]);

            $this->storageLink();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Storage Link.
     */
    private function storageLink()
    {
        Artisan::call('storage:link');
    }

    /**
     * Generate New Application Key
     */
    public function generateKey()
    {
        try {
            Artisan::call('key:generate');
        } catch (Exception $e) {
        }
    }
}
