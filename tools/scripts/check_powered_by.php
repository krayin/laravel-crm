<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Webkul\ThemeManager\Models\ThemeConfig;

$config = ThemeConfig::first();

echo "========================================\n";
echo "POWERED BY - DIAGNÓSTICO\n";
echo "========================================\n\n";

echo "Valor no banco:\n";
echo "  login_show_powered_by: " . ($config->login_show_powered_by ?? 'NULL') . "\n";
echo "  Tipo: " . gettype($config->login_show_powered_by) . "\n";
echo "  É TRUE?: " . ($config->login_show_powered_by === true ? 'SIM' : 'NÃO') . "\n";
echo "  É FALSE?: " . ($config->login_show_powered_by === false ? 'SIM' : 'NÃO') . "\n";
echo "  É 1?: " . ($config->login_show_powered_by === 1 ? 'SIM' : 'NÃO') . "\n";
echo "  É 0?: " . ($config->login_show_powered_by === 0 ? 'SIM' : 'NÃO') . "\n";
echo "  É '1'?: " . ($config->login_show_powered_by === '1' ? 'SIM' : 'NÃO') . "\n";
echo "  É '0'?: " . ($config->login_show_powered_by === '0' ? 'SIM' : 'NÃO') . "\n\n";

echo "Como o Blade interpreta:\n";
echo "  if(\$config->login_show_powered_by): " . ($config->login_show_powered_by ? 'VERDADEIRO' : 'FALSO') . "\n";
echo "  if(!\$config->login_show_powered_by): " . (!$config->login_show_powered_by ? 'VERDADEIRO' : 'FALSO') . "\n\n";

echo "========================================\n";
