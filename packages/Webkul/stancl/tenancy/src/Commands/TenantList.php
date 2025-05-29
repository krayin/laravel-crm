<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Contracts\Tenant;

class TenantList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List tenants.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Listing all tenants.');
        tenancy()
            ->query()
            ->cursor()
            ->each(function (Tenant $tenant) {
                if ($tenant->domains) {
                    $this->line("[Tenant] {$tenant->getTenantKeyName()}: {$tenant->getTenantKey()} @ " . implode('; ', $tenant->domains->pluck('domain')->toArray() ?? []));
                } else {
                    $this->line("[Tenant] {$tenant->getTenantKeyName()}: {$tenant->getTenantKey()}");
                }
            });
    }
}
