<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $pass = config('database.connections.mysql.password');
    $user = config('database.connections.mysql.username');
    echo "Reverting user [$user] to mysql_native_password...\n";

    // Fallback to legacy plugin
    \Illuminate\Support\Facades\DB::statement("ALTER USER '$user'@'localhost' IDENTIFIED WITH mysql_native_password BY '$pass'");
    \Illuminate\Support\Facades\DB::statement("FLUSH PRIVILEGES");

    echo "SUCCESS: User reverted to mysql_native_password.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
