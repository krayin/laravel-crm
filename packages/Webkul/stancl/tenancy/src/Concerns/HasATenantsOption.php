<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Concerns;

use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Input\InputOption;

trait HasATenantsOption
{
    protected function getOptions()
    {
        return array_merge([
            ['tenants', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, '', null],
        ], parent::getOptions());
    }

    protected function getTenants(): LazyCollection
    {
        return tenancy()
            ->query()
            ->when($this->option('tenants'), function ($query) {
                $query->whereIn(tenancy()->model()->getTenantKeyName(), $this->option('tenants'));
            })
            ->cursor();
    }

    public function __construct()
    {
        parent::__construct();

        $this->specifyParameters();
    }
}
