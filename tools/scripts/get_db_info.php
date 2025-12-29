<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$config = \Webkul\ThemeManager\Models\ThemeConfig::first();

echo "=== DATABASE STATE ===\n";
echo 'ID: '.$config->id."\n";
echo 'Login Card Enabled: '.($config->login_card_enabled ? 'TRUE' : 'FALSE')."\n";
echo 'Custom Code Size: '.strlen($config->login_card_custom_code ?? '')." bytes\n";
echo 'Has <style>: '.(strpos($config->login_card_custom_code, '<style>') !== false ? 'YES' : 'NO')."\n";
echo 'Has <script>: '.(strpos($config->login_card_custom_code, '<script>') !== false ? 'YES' : 'NO')."\n";
echo "\n=== CUSTOM CODE PREVIEW (first 500 chars) ===\n";
echo substr($config->login_card_custom_code, 0, 500)."...\n";
