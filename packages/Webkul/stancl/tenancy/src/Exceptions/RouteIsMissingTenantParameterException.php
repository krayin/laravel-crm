<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Exception;
use Stancl\Tenancy\Resolvers\PathTenantResolver;

class RouteIsMissingTenantParameterException extends Exception
{
    public function __construct()
    {
        $parameter = PathTenantResolver::$tenantParameterName;

        parent::__construct("The route's first argument is not the tenant id (configured paramter name: $parameter).");
    }
}
