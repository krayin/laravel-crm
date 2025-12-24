<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE UPLOAD E CONFIGURAÇÃO ===\n\n";

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();

echo "1. CONFIGURAÇÃO ATUAL:\n";
echo "   Tema Ativo: " . ($config->is_active ? "SIM" : "NÃO") . "\n";
echo "   Cor Primary: " . $config->color_primary . "\n";
echo "   Logo Main: " . ($config->logo_main ?: "não configurado") . "\n";
echo "   Logo Light: " . ($config->logo_light ?: "não configurado") . "\n";
echo "   Logo Icon: " . ($config->logo_icon ?: "não configurado") . "\n\n";

echo "2. DIRETÓRIOS:\n";
$storageDir = storage_path('app/public/theme-manager');
$publicDir = public_path('storage/theme-manager');

echo "   Storage: " . ($storageDir) . " - " . (is_dir($storageDir) ? "EXISTE" : "NÃO EXISTE") . "\n";
echo "   Public: " . ($publicDir) . " - " . (is_dir($publicDir) ? "EXISTE" : "NÃO EXISTE") . "\n\n";

if (is_dir($storageDir)) {
    $files = array_diff(scandir($storageDir), ['.', '..']);
    echo "   Arquivos em storage: " . (count($files) > 0 ? implode(", ", $files) : "VAZIO") . "\n\n";
}

echo "3. PERMISSÕES:\n";
echo "   Storage writable: " . (is_writable($storageDir) ? "SIM" : "NÃO") . "\n";
echo "   Public writable: " . (is_writable(public_path('storage')) ? "SIM" : "NÃO") . "\n\n";

echo "4. SYMLINK:\n";
$symlinkExists = is_link(public_path('storage'));
echo "   public/storage é symlink: " . ($symlinkExists ? "SIM" : "NÃO") . "\n";
if ($symlinkExists) {
    echo "   Aponta para: " . readlink(public_path('storage')) . "\n";
}

echo "\n5. HELPER THEME:\n";
try {
    $helper = app('theme');
    echo "   Helper registrado: SIM\n";
    echo "   Tema ativo via helper: " . ($helper->isActive() ? "SIM" : "NÃO") . "\n";
} catch (\Exception $e) {
    echo "   ERRO: " . $e->getMessage() . "\n";
}
