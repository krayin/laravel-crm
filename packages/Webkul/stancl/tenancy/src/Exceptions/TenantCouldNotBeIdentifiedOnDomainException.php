<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;

class TenantCouldNotBeIdentifiedOnDomainException extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct($domain)
    {
        parent::__construct("Tenant could not be identified on domain $domain");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified on this domain')
            ->setSolutionDescription('Did you forget to create a tenant for this domain?')
            ->setDocumentationLinks([
                'Creating Tenants' => 'https://tenancyforlaravel.com/docs/v3/tenants/',
            ]);
    }
}
