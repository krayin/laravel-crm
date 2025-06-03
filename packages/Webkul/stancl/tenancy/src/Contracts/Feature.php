<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Contracts;

use Stancl\Tenancy\Tenancy;

/** Additional features, like Telescope tags and tenant redirects. */
interface Feature
{
    public function bootstrap(Tenancy $tenancy): void;
}
