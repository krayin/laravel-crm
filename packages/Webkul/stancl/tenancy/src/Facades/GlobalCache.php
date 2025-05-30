<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Facades;

use Illuminate\Support\Facades\Cache;

class GlobalCache extends Cache
{
    protected static function getFacadeAccessor()
    {
        return 'globalCache';
    }
}
