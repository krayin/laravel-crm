<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // We use a raw connection to check mysql.user locally if possible, 
    // but if the main connection fails, we can't easily query via Laravel's DB facade 
    // if that facade is what's broken.
    // However, the error happens on 'select * from core_config', so connection fails during handshake.

    // Attempt to connect using mysqli directly to bypass Laravel for diagnostic
    $host = config('database.connections.mysql.host');
    $user = config('database.connections.mysql.username');
    $pass = config('database.connections.mysql.password');
    $port = config('database.connections.mysql.port');

    echo "Configured Connection: $user@$host:$port\n";

    $mysqli = new mysqli($host, $user, $pass, "mysql", $port);

    if ($mysqli->connect_error) {
        echo "MYSQLI CONNECT ERROR: " . $mysqli->connect_error . "\n";
    } else {
        echo "MYSQLI CONNECT SUCCEESS!\n";
        $res = $mysqli->query("SELECT user, host, plugin FROM user WHERE user='$user'");
        while ($row = $res->fetch_assoc()) {
            print_r($row);
        }
    }

} catch (\Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage() . "\n";
}
