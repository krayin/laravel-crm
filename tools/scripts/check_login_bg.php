<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$config = \Webkul\ThemeManager\Models\ThemeConfig::first();

echo "========================================\n";
echo "THEME CONFIG - LOGIN BACKGROUND\n";
echo "========================================\n\n";

echo "is_active: " . ($config->is_active ? 'TRUE' : 'FALSE') . "\n";
echo "login_bg_image: " . ($config->login_bg_image ?? 'NULL') . "\n";
echo "login_bg_zoom: " . ($config->login_bg_zoom ?? 'NULL') . "\n";
echo "login_bg_opacity: " . ($config->login_bg_opacity ?? 'NULL') . "\n";
echo "login_show_powered_by: " . ($config->login_show_powered_by ? 'TRUE' : 'FALSE') . "\n\n";

if ($config->login_bg_image) {
    $path = storage_path('app/public/theme-manager/' . $config->login_bg_image);
    echo "File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    if (file_exists($path)) {
        echo "File size: " . round(filesize($path) / 1024, 2) . " KB\n";
        echo "Full path: $path\n";
    }
}

echo "\n========================================\n";
