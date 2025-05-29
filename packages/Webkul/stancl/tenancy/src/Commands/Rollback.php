<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Migrations\Migrator;
use Stancl\Tenancy\Concerns\DealsWithMigrations;
use Stancl\Tenancy\Concerns\ExtendsLaravelCommand;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Events\DatabaseRolledBack;
use Stancl\Tenancy\Events\RollingBackDatabase;

class Rollback extends RollbackCommand
{
    use HasATenantsOption, DealsWithMigrations, ExtendsLaravelCommand;

    protected static function getTenantCommandName(): string
    {
        return 'tenants:rollback';
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migrations for tenant(s).';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct($migrator);

        $this->specifyTenantSignature();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (config('tenancy.migration_parameters') as $parameter => $value) {
            if (! $this->input->hasParameterOption($parameter)) {
                $this->input->setOption(ltrim($parameter, '-'), $value);
            }
        }

        if (! $this->confirmToProceed()) {
            return;
        }

        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant->getTenantKey()}");

            event(new RollingBackDatabase($tenant));

            // Rollback
            parent::handle();

            event(new DatabaseRolledBack($tenant));
        });
    }
}
