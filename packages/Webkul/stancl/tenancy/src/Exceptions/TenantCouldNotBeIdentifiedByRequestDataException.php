<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;

class TenantCouldNotBeIdentifiedByRequestDataException extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct($tenant_id)
    {
        parent::__construct("Tenant could not be identified by request data with payload: $tenant_id");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified with this request data')
            ->setSolutionDescription('Did you forget to create a tenant with this id?')
            ->setDocumentationLinks([
                'Creating Tenants' => 'https://tenancyforlaravel.com/docs/v3/tenants/',
            ]);
    }
}
