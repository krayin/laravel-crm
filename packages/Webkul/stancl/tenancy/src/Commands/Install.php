<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install stancl/tenancy.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Installing stancl/tenancy...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
            '--tag' => 'config',
        ]);
        $this->info('✔️  Created config/tenancy.php');

        if (! file_exists(base_path('routes/tenant.php'))) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
                '--tag' => 'routes',
            ]);
            $this->info('✔️  Created routes/tenant.php');
        } else {
            $this->info('Found routes/tenant.php.');
        }

        $this->callSilent('vendor:publish', [
            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
            '--tag' => 'providers',
        ]);
        $this->info('✔️  Created TenancyServiceProvider.php');

        $this->callSilent('vendor:publish', [
            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
            '--tag' => 'migrations',
        ]);
        $this->info('✔️  Created migrations. Remember to run [php artisan migrate]!');

        if (! is_dir(database_path('migrations/tenant'))) {
            mkdir(database_path('migrations/tenant'));
            $this->info('✔️  Created database/migrations/tenant folder.');
        }

        $this->comment('✨️ stancl/tenancy installed successfully.');
    }
}
