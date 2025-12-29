<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ESTADO ATUAL DO BANCO DE DADOS ===\n\n";

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();

echo 'Logo Main: '.($config->logo_main ?: '❌ VAZIO')."\n";
echo 'Logo Light: '.($config->logo_light ?: '❌ VAZIO')."\n";
echo 'Logo Icon: '.($config->logo_icon ?: '❌ VAZIO')."\n";
echo 'Favicon: '.($config->favicon ?: '❌ VAZIO')."\n";

echo "\n=== ARQUIVOS NA PASTA storage/app/public/theme-manager ===\n\n";

$storagePath = storage_path('app/public/theme-manager');
if (is_dir($storagePath)) {
    $files = array_diff(scandir($storagePath), ['.', '..', '.gitkeep']);
    foreach ($files as $file) {
        $fullPath = $storagePath.'/'.$file;
        $size = filesize($fullPath);
        echo "  $file (".round($size / 1024, 2)." KB)\n";
    }
} else {
    echo "  ❌ Pasta não existe!\n";
}

echo "\n=== ANÁLISE ===\n\n";

if (! $config->logo_main) {
    echo "⚠️  LOGO_MAIN está VAZIO no banco!\n";
    echo "   → O CSS não vai substituir o logo padrão do Krayin\n";
    echo "   → Você precisa fazer upload no campo 'Logo Main'\n";
} else {
    $filePath = $storagePath.'/'.$config->logo_main;
    if (file_exists($filePath)) {
        echo "✅ LOGO_MAIN: arquivo existe em storage\n";
        echo '   URL: '.asset('storage/theme-manager/'.$config->logo_main)."\n";
    } else {
        echo "❌ LOGO_MAIN: arquivo NÃO existe em storage!\n";
        echo "   Esperado: $filePath\n";
    }
}

if ($config->logo_light) {
    $filePath = $storagePath.'/'.$config->logo_light;
    if (file_exists($filePath)) {
        echo "✅ LOGO_LIGHT: arquivo existe em storage\n";
        echo '   URL: '.asset('storage/theme-manager/'.$config->logo_light)."\n";
    } else {
        echo "❌ LOGO_LIGHT: arquivo NÃO existe em storage!\n";
        echo "   Esperado: $filePath\n";
    }
}

if ($config->favicon) {
    $filePath = $storagePath.'/'.$config->favicon;
    if (file_exists($filePath)) {
        echo "✅ FAVICON: arquivo existe em storage\n";
        echo '   URL: '.asset('storage/theme-manager/'.$config->favicon)."\n";
    } else {
        echo "❌ FAVICON: arquivo NÃO existe em storage!\n";
        echo "   Esperado: $filePath\n";
    }
}
