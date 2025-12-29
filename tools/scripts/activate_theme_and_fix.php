<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATIVANDO TEMA E CORRIGINDO CORES ===\n\n";

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();

echo "ANTES:\n";
echo '  Ativo: '.($config->is_active ? 'SIM' : 'NÃO')."\n";
echo '  Cor Primary: '.$config->color_primary."\n\n";

// Ativar tema e corrigir cores
$config->update([
    'is_active'           => true,
    'color_primary'       => '#1E40AF',       // Azul Krayin
    'color_primary_dark'  => '#1E3A8A',  // Azul escuro
    'color_primary_light' => '#3B82F6', // Azul claro
    'color_success'       => '#10B981',
    'color_warning'       => '#F59E0B',
    'color_danger'        => '#EF4444',
]);

echo "DEPOIS:\n";
echo '  Ativo: '.($config->is_active ? 'SIM' : 'NÃO')."\n";
echo '  Cor Primary: '.$config->color_primary."\n\n";

// Limpar cache
cache()->forget('theme_config');
app('theme')->clearCache();

echo "✓ Tema ATIVADO\n";
echo "✓ Cores corrigidas para azul Krayin\n";
echo "✓ Cache limpo\n\n";
echo "AGORA recarregue a página com Ctrl+F5\n";
