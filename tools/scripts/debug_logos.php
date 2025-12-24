<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG DE LOGOS - INVESTIGAÇÃO COMPLETA ===\n\n";

// 1. Verificar configuração no banco
echo "1. CONFIGURAÇÃO NO BANCO DE DADOS:\n";
$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "   ID: " . $config->id . "\n";
echo "   Tema Ativo: " . ($config->is_active ? "SIM" : "NÃO") . "\n";
echo "   Logo Main: " . ($config->logo_main ?: "NÃO CONFIGURADO") . "\n";
echo "   Logo Light: " . ($config->logo_light ?: "NÃO CONFIGURADO") . "\n";
echo "   Logo Icon: " . ($config->logo_icon ?: "NÃO CONFIGURADO") . "\n";
echo "   Favicon: " . ($config->favicon ?: "NÃO CONFIGURADO") . "\n";
echo "\n";

// 2. Verificar arquivos em storage
echo "2. ARQUIVOS EM STORAGE:\n";
$storagePath = storage_path('app/public/theme-manager');
echo "   Path: $storagePath\n";

if (is_dir($storagePath)) {
    $files = array_diff(scandir($storagePath), ['.', '..', '.gitkeep']);
    echo "   Arquivos encontrados: " . count($files) . "\n";

    if (count($files) > 0) {
        foreach ($files as $file) {
            $fullPath = $storagePath . '/' . $file;
            $size = filesize($fullPath);
            echo "     - $file (" . round($size/1024, 2) . " KB)\n";
        }
    } else {
        echo "     ⚠️  Nenhum arquivo encontrado!\n";
    }
} else {
    echo "   ❌ DIRETÓRIO NÃO EXISTE!\n";
}
echo "\n";

// 3. Verificar symlink
echo "3. SYMLINK public/storage:\n";
$publicStorage = public_path('storage');
if (is_link($publicStorage)) {
    echo "   É symlink: SIM ✓\n";
    echo "   Aponta para: " . readlink($publicStorage) . "\n";

    // Verificar se theme-manager existe via symlink
    $themeManagerPublic = $publicStorage . '/theme-manager';
    echo "   public/storage/theme-manager existe: " . (is_dir($themeManagerPublic) ? "SIM ✓" : "NÃO ✗") . "\n";

    if (is_dir($themeManagerPublic)) {
        $publicFiles = array_diff(scandir($themeManagerPublic), ['.', '..', '.gitkeep']);
        echo "   Arquivos acessíveis via web: " . count($publicFiles) . "\n";
    }
} else {
    echo "   ❌ NÃO É SYMLINK!\n";
    echo "   Tipo: " . (is_dir($publicStorage) ? "Diretório comum" : "Não existe") . "\n";
}
echo "\n";

// 4. Testar URLs de acesso
echo "4. URLs DE ACESSO:\n";
if ($config->logo_main) {
    $url = asset('storage/theme-manager/' . $config->logo_main);
    echo "   Logo Main URL: $url\n";

    // Verificar se arquivo existe fisicamente
    $publicPath = public_path('storage/theme-manager/' . $config->logo_main);
    echo "   Arquivo existe: " . (file_exists($publicPath) ? "SIM ✓" : "NÃO ✗") . "\n";
}
echo "\n";

// 5. Verificar CSS que será injetado
echo "5. CSS QUE SERÁ INJETADO:\n";
if ($config->is_active) {
    echo "   Tema está ATIVO - CSS será injetado ✓\n";

    if ($config->logo_main) {
        $cssUrl = asset('storage/theme-manager/' . $config->logo_main);
        echo "   CSS para logo_main:\n";
        echo "   img[src*=\"logo.svg\"]:not([src*=\"dark-logo\"]):not([src*=\"mobile\"]) {\n";
        echo "       content: url('$cssUrl') !important;\n";
        echo "   }\n";
    } else {
        echo "   ⚠️  Logo Main não configurado - nenhum CSS será gerado\n";
    }
} else {
    echo "   ❌ Tema está DESATIVADO - CSS NÃO será injetado\n";
}
echo "\n";

// 6. Verificar seletores no HTML do Krayin
echo "6. SELETORES NO HTML DO KRAYIN:\n";
echo "   Procurando por imagens com 'logo' no src...\n";
$adminPath = base_path('packages/Webkul/Admin/src/Resources/views');

// Procurar por tags <img> com logo
$grepCommand = "grep -r \"logo.svg\" " . escapeshellarg($adminPath) . " 2>/dev/null | head -5";
echo "   Comando: find logo.svg no Admin package\n";
echo "   (Verificar manualmente se existe img[src*='logo.svg'] no HTML)\n";
echo "\n";

// 7. Debug do Middleware
echo "7. MIDDLEWARE ThemeMiddleware:\n";
$middlewareGroups = app('router')->getMiddlewareGroups();
if (isset($middlewareGroups['web'])) {
    $hasTheme = false;
    foreach ($middlewareGroups['web'] as $middleware) {
        if (str_contains($middleware, 'ThemeMiddleware')) {
            $hasTheme = true;
            echo "   ✓ ThemeMiddleware está no grupo 'web'\n";
            break;
        }
    }
    if (!$hasTheme) {
        echo "   ❌ ThemeMiddleware NÃO está no grupo 'web'\n";
    }
} else {
    echo "   ❌ Grupo 'web' não encontrado\n";
}
echo "\n";

// 8. Sugestões de debug
echo "=== DIAGNÓSTICO ===\n\n";

$issues = [];

if (!$config->is_active) {
    $issues[] = "⚠️  TEMA DESATIVADO - Ative em 'Theme Active' = Yes";
}

if (!$config->logo_main && !$config->logo_light && !$config->logo_icon) {
    $issues[] = "⚠️  NENHUM LOGO CONFIGURADO - Faça upload de pelo menos um logo";
}

if (!is_link(public_path('storage'))) {
    $issues[] = "❌ SYMLINK AUSENTE - Execute: php artisan storage:link";
}

if (count($issues) > 0) {
    echo "PROBLEMAS ENCONTRADOS:\n";
    foreach ($issues as $issue) {
        echo "  $issue\n";
    }
} else {
    echo "✓ Nenhum problema óbvio detectado\n";
    echo "\nPróximas investigações:\n";
    echo "1. Verificar console do navegador (F12) para erros de CSS\n";
    echo "2. Inspecionar elemento <img> do logo no navegador\n";
    echo "3. Verificar se CSS foi realmente injetado no <head>\n";
    echo "4. Testar URL direta: http://127.0.0.1:8000/storage/theme-manager/NOME_DO_ARQUIVO\n";
}
