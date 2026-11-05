<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/packages',
    ])
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/storage',
    ]);
