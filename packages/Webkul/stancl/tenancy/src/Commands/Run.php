<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

use Illuminate\Console\Command;

class Run extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a command for tenant(s)';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "tenants:run {commandname : The command's name.}
                            {--tenants=* : The tenant(s) to run the command for. Default: all}
                            {--argument=* : The arguments to pass to the command. Default: none}
                            {--option=* : The options to pass to the command. Default: none}";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant->getTenantKey()}");

            $callback = function ($prefix = '') {
                return function ($arguments, $argument) use ($prefix) {
                    [$key, $value] = explode('=', $argument, 2);
                    $arguments[$prefix . $key] = $value;

                    return $arguments;
                };
            };

            // Turns ['foo=bar', 'abc=xyz=zzz'] into ['foo' => 'bar', 'abc' => 'xyz=zzz']
            $arguments = array_reduce($this->option('argument'), $callback(), []);

            // Turns ['foo=bar', 'abc=xyz=zzz'] into ['--foo' => 'bar', '--abc' => 'xyz=zzz']
            $options = array_reduce($this->option('option'), $callback('--'), []);

            // Run command
            $this->call($this->argument('commandname'), array_merge($arguments, $options));
        });
    }
}
