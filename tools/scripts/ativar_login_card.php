<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Webkul\ThemeManager\Models\ThemeConfig;

echo "========================================\n";
echo "ATIVAR LOGIN CARD - Teste\n";
echo "========================================\n\n";

$config = ThemeConfig::first();

echo "Estado ATUAL:\n";
echo "  login_card_enabled: " . ($config->login_card_enabled ? 'TRUE' : 'FALSE') . "\n";
echo "  login_card_title: " . ($config->login_card_title ?? 'NULL') . "\n";
echo "  login_card_subtitle: " . ($config->login_card_subtitle ?? 'NULL') . "\n";
echo "  login_card_sparkles: " . ($config->login_card_sparkles ? 'TRUE' : 'FALSE') . "\n";
echo "  login_card_help_link: " . ($config->login_card_help_link ? 'TRUE' : 'FALSE') . "\n";
echo "  login_card_support_email: " . ($config->login_card_support_email ?? 'NULL') . "\n\n";

echo "Ativando Login Card com valores de teste...\n";

$config->login_card_enabled = true;
$config->login_card_title = 'Olá! 👋';
$config->login_card_subtitle = 'Bem-vindo de volta ao CRM';
$config->login_card_sparkles = true;
$config->login_card_help_link = true;
$config->login_card_support_email = 'suporte@teste.com.br';
$config->login_card_overlay_color = 'rgba(30, 64, 175, 0.7)'; // Azul semi-transparente

$config->save();

echo "✅ Login Card ativado!\n\n";

echo "Valores ATUALIZADOS:\n";
echo "  login_card_enabled: " . ($config->login_card_enabled ? 'TRUE ✅' : 'FALSE') . "\n";
echo "  login_card_title: \"" . $config->login_card_title . "\"\n";
echo "  login_card_subtitle: \"" . $config->login_card_subtitle . "\"\n";
echo "  login_card_sparkles: " . ($config->login_card_sparkles ? 'TRUE ✨' : 'FALSE') . "\n";
echo "  login_card_help_link: " . ($config->login_card_help_link ? 'TRUE' : 'FALSE') . "\n";
echo "  login_card_support_email: \"" . $config->login_card_support_email . "\"\n";
echo "  login_card_overlay_color: \"" . $config->login_card_overlay_color . "\"\n\n";

echo "========================================\n";
echo "PRÓXIMO PASSO:\n";
echo "========================================\n";
echo "1. Acesse: http://127.0.0.1:8000/admin/login\n";
echo "2. Pressione F12 para abrir Console\n";
echo "3. Procure por logs:\n";
echo "   🎨 ThemeManager: Aplicando Login Card customizado...\n";
echo "   ✓ Login card encontrado\n";
echo "   ✓ Título e subtítulo aplicados\n";
echo "   ✓ Sparkles aplicados\n";
echo "   ✓ Link de ajuda adicionado\n";
echo "   ✅ ThemeManager: Login Card customizado aplicado!\n";
echo "========================================\n";
