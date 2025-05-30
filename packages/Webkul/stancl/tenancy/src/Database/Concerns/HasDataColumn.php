<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\VirtualColumn\VirtualColumn;

/**
 * Extends VirtualColumn for backwards compatibility. This trait will be removed in v4.
 */
trait HasDataColumn
{
    use VirtualColumn;
}
