<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$classes = [
    \App\Support\BrandKitResolver::class,
    \App\Support\CssValidator::class,
    \App\Models\BrandKitOverride::class,
    \App\Models\BrandKitCustomCss::class,
    \App\Models\BrandKitSnapshot::class,
];

foreach ($classes as $class) {
    $short = class_basename($class);
    echo class_exists($class) ? "OK {$short}" : "FALTA {$short}";
    echo PHP_EOL;
}
