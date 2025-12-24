<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Webkul\ThemeManager\Models\ThemeConfig;

$config = ThemeConfig::first();

echo "========================================\n";
echo "TESTE MANUAL: Setando login_bg_image\n";
echo "========================================\n\n";

echo "Valor ANTES: " . ($config->login_bg_image ?? 'NULL') . "\n";

// Usar arquivo que JÁ existe no storage (logo)
$config->login_bg_image = '1766361510_logo_main_xpPo9ckg.png';
$config->login_bg_zoom = 150;
$config->login_bg_opacity = 30;
$config->save();

// Recarregar do banco
$config = ThemeConfig::first();

echo "Valor DEPOIS:\n";
echo "  login_bg_image: " . ($config->login_bg_image ?? 'NULL') . "\n";
echo "  login_bg_zoom: " . ($config->login_bg_zoom ?? 'NULL') . "\n";
echo "  login_bg_opacity: " . ($config->login_bg_opacity ?? 'NULL') . "\n\n";

if ($config->login_bg_image === '1766361510_logo_main_xpPo9ckg.png') {
    echo "✅ SUCESSO: Valor salvo no banco!\n\n";
    echo "PRÓXIMOS PASSOS:\n";
    echo "1. Limpar cache: php artisan view:clear && php artisan cache:clear\n";
    echo "2. Acessar: http://127.0.0.1:8000/admin/login\n";
    echo "3. Pressionar F12 e verificar Console\n";
    echo "4. Verificar se background aparece (imagem do logo como teste)\n\n";
    echo "Se background aparecer → CSS/JS funcionando ✅\n";
    echo "Depois resetar: login_bg_image = NULL\n";
} else {
    echo "❌ FALHA: Valor não salvou!\n";
}

echo "\n========================================\n";
