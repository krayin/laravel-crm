<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Webkul\ThemeManager\Models\ThemeConfig;

$config = ThemeConfig::first();

echo "========================================\n";
echo "DIAGNÓSTICO: is_active Select\n";
echo "========================================\n\n";

echo "Valor no banco:\n";
echo "  is_active (raw): " . var_export($config->is_active, true) . "\n";
echo "  Tipo: " . gettype($config->is_active) . "\n\n";

echo "Comparações:\n";
echo "  == 0: " . ($config->is_active == 0 ? 'TRUE' : 'FALSE') . "\n";
echo "  == 1: " . ($config->is_active == 1 ? 'TRUE' : 'FALSE') . "\n";
echo "  === 0: " . ($config->is_active === 0 ? 'TRUE' : 'FALSE') . "\n";
echo "  === 1: " . ($config->is_active === 1 ? 'TRUE' : 'FALSE') . "\n";
echo "  === true: " . ($config->is_active === true ? 'TRUE' : 'FALSE') . "\n";
echo "  === false: " . ($config->is_active === false ? 'TRUE' : 'FALSE') . "\n\n";

echo "Atributo 'selected' será aplicado em:\n";
if (old('is_active', $config->is_active) == 0) {
    echo "  → Option value=\"0\" (NÃO)\n";
} elseif (old('is_active', $config->is_active) == 1) {
    echo "  → Option value=\"1\" (SIM)\n";
} else {
    echo "  → NENHUMA! (Problema)\n";
}

echo "\n========================================\n";
