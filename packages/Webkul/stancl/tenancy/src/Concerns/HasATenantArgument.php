<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Concerns;

use Symfony\Component\Console\Input\InputArgument;

trait HasATenantArgument
{
    protected function getArguments()
    {
        return array_merge([
            ['tenant', InputArgument::REQUIRED, 'Tenant id', null],
        ], parent::getArguments());
    }

    protected function getTenants(): array
    {
        return [tenancy()->find($this->argument('tenant'))];
    }

    public function __construct()
    {
        parent::__construct();

        $this->specifyParameters();
    }
}
