<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== BrandKitRepository Tests ===" . PHP_EOL . PHP_EOL;

try {
    $repo = app(\App\Repositories\BrandKitRepository::class);
    echo "1. Repository resolved: OK" . PHP_EOL;

    // Test setOverride
    $override = $repo->setOverride('global', 'default', 'color_primary', '#FF0000', 1);
    echo "2. setOverride created: " . ($override->override_key === 'color_primary' ? 'OK' : 'FAIL') . PHP_EOL;

    // Verify in DB
    $first = \App\Models\BrandKitOverride::first();
    echo "3. Override in DB: " . ($first && $first->override_key === 'color_primary' ? 'OK' : 'FAIL') . PHP_EOL;
    echo "   -> override_key: {$first->override_key}" . PHP_EOL;
    echo "   -> value: {$first->value}" . PHP_EOL;

    // Test addCustomCss
    $css = $repo->addCustomCss('global', 'default', 'Teste CSS', '#brand-kit-scope .btn{color:red;}', 'admin', 1);
    echo "4. addCustomCss created: " . ($css->name === 'Teste CSS' ? 'OK' : 'FAIL') . PHP_EOL;

    // Count CSS entries
    $count = \App\Models\BrandKitCustomCss::count();
    echo "5. CustomCss count: {$count}" . PHP_EOL;

    // Test createSnapshot
    $snapshot = $repo->createSnapshot('global', 'default', 'Test Snapshot', 1, false);
    echo "6. createSnapshot: " . ($snapshot->name === 'Test Snapshot' ? 'OK' : 'FAIL') . PHP_EOL;

    echo PHP_EOL . "=== All Tests Complete ===" . PHP_EOL;

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
