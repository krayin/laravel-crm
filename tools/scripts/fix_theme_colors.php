<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRIGINDO CORES DO TEMA ===\n\n";

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();

echo "Cores ANTES:\n";
echo '  Primary: '.$config->color_primary."\n";
echo '  Primary Dark: '.$config->color_primary_dark."\n";
echo '  Primary Light: '.$config->color_primary_light."\n\n";

// Resetar para cores padrão do Krayin (azul)
$config->update([
    'color_primary'       => '#1E40AF',       // Azul Krayin
    'color_primary_dark'  => '#1E3A8A',  // Azul escuro
    'color_primary_light' => '#3B82F6', // Azul claro
    'color_success'       => '#10B981',       // Verde
    'color_warning'       => '#F59E0B',       // Amarelo
    'color_danger'        => '#EF4444',        // Vermelho
]);

echo "Cores DEPOIS:\n";
echo '  Primary: '.$config->color_primary."\n";
echo '  Primary Dark: '.$config->color_primary_dark."\n";
echo '  Primary Light: '.$config->color_primary_light."\n\n";

// Limpar cache
app('theme')->clearCache();

echo "✓ Cores resetadas para padrão Krayin\n";
echo "✓ Cache limpo\n\n";
echo "AGORA o botão Save Settings deve estar visível!\n";
