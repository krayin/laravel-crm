<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Contracts;

interface UniqueIdentifierGenerator
{
    /**
     * Generate a unique identifier.
     */
    public static function generate($resource): string;
}
