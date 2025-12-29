<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Webkul\ThemeManager\Models\ThemeConfig;

$config = ThemeConfig::first();

echo "========================================\n";
echo "TESTE: Salvamento Manual login_bg_image\n";
echo "========================================\n\n";

echo 'Valor ANTES: '.($config->login_bg_image ?? 'NULL')."\n";

// Simular salvamento direto
$config->login_bg_image = 'test_manual_upload.jpg';
$config->save();

// Recarregar do banco
$config = ThemeConfig::first();

echo 'Valor DEPOIS: '.($config->login_bg_image ?? 'NULL')."\n";

if ($config->login_bg_image === 'test_manual_upload.jpg') {
    echo "\n✅ SUCESSO: Salvamento manual funciona!\n";
    echo "   Problema está no Repository ou Upload.\n";

    // Limpar teste
    $config->login_bg_image = null;
    $config->save();
    echo "   Valor resetado para NULL.\n";
} else {
    echo "\n❌ FALHA: Salvamento manual não funciona!\n";
    echo "   Problema no Model ou estrutura do banco.\n";
}

echo "\n========================================\n";
