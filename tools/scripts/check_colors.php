<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();

echo "=== CONFIGURAÇÃO ATUAL DO TEMA ===\n\n";
echo 'Ativo: '.($config->is_active ? 'SIM' : 'NÃO')."\n";
echo 'Cor Primary: '.$config->color_primary."\n";
echo 'Cor Primary Dark: '.$config->color_primary_dark."\n";
echo 'Cor Primary Light: '.$config->color_primary_light."\n\n";

echo "PROBLEMA IDENTIFICADO:\n";
if ($config->color_primary === '#ffffff') {
    echo "✗ A cor primária é BRANCO (#ffffff)\n";
    echo "✗ Isso torna o botão invisível em fundo branco!\n";
} else {
    echo "✓ Cor primária OK\n";
}
