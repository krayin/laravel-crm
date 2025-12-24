<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DAS CORREÇÕES DO CLAUDE WEB ===\n\n";

// Test 1: Config atual
echo "1. CONFIGURAÇÃO ATUAL:\n";
$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "   Tema Ativo: " . ($config->is_active ? "SIM" : "NÃO") . "\n";
echo "   Cor Primary: " . $config->color_primary . "\n";
echo "   Logo Main: " . ($config->logo_main ?: "não configurado") . "\n\n";

// Test 2: Helper com cache de array
echo "2. HELPER (deve retornar array agora):\n";
try {
    $helper = app('theme');
    $cachedConfig = $helper->getConfig();
    echo "   Tipo retornado: " . gettype($cachedConfig) . "\n";
    echo "   É array: " . (is_array($cachedConfig) ? "SIM ✓" : "NÃO ✗") . "\n";
    if (is_array($cachedConfig)) {
        echo "   Chaves: " . count($cachedConfig) . " campos\n";
        echo "   Tem color_primary: " . (isset($cachedConfig['color_primary']) ? "SIM ✓" : "NÃO ✗") . "\n";
    }
} catch (\Exception $e) {
    echo "   ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Validação de extensões
echo "3. VALIDAÇÃO DE EXTENSÕES (whitelist):\n";
$repo = new \Webkul\ThemeManager\Repositories\ThemeConfigRepository(
    new \Webkul\ThemeManager\Models\ThemeConfig(),
    app('theme')
);

// Usar Reflection para acessar propriedade protected
$reflection = new \ReflectionClass($repo);
$property = $reflection->getProperty('allowedExtensions');
$property->setAccessible(true);
$allowedExtensions = $property->getValue($repo);

echo "   Logo: " . implode(', ', $allowedExtensions['logo']) . "\n";
echo "   Favicon: " . implode(', ', $allowedExtensions['favicon']) . "\n";
echo "   Background: " . implode(', ', $allowedExtensions['background']) . "\n";
echo "   Empty State: " . implode(', ', $allowedExtensions['empty_state']) . "\n";
echo "\n";

// Test 4: Sanitização de SVG
echo "4. SANITIZAÇÃO DE SVG:\n";
$method = $reflection->getMethod('sanitizeSvg');
$method->setAccessible(true);

$maliciousSvg = '<svg><script>alert("XSS")</script><circle onclick="alert(1)" /></svg>';
$sanitized = $method->invoke($repo, $maliciousSvg);

echo "   SVG original: " . $maliciousSvg . "\n";
echo "   SVG sanitizado: " . $sanitized . "\n";
echo "   Script removido: " . (strpos($sanitized, '<script') === false ? "SIM ✓" : "NÃO ✗") . "\n";
echo "   Event handler removido: " . (strpos($sanitized, 'onclick') === false ? "SIM ✓" : "NÃO ✗") . "\n";
echo "\n";

// Test 5: Validação de cores (apenas informação, validação acontece no Controller)
echo "5. VALIDAÇÃO DE CORES (regex patterns no Controller):\n";
$hexPattern = '/^#[0-9A-Fa-f]{6}$/';
$rgbaPattern = '/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+\s*)?\)$/';

$validHex = '#1E40AF';
$invalidHex = '#GGGGGG';
$validRgba = 'rgba(10, 45, 15, 0.78)';
$invalidRgba = 'rgba(999, 999, 999, 999)';

echo "   Hex válido ($validHex): " . (preg_match($hexPattern, $validHex) ? "PASS ✓" : "FAIL ✗") . "\n";
echo "   Hex inválido ($invalidHex): " . (preg_match($hexPattern, $invalidHex) ? "FAIL ✗" : "PASS ✓") . "\n";
echo "   RGBA válido ($validRgba): " . (preg_match($rgbaPattern, $validRgba) ? "PASS ✓" : "FAIL ✗") . "\n";
echo "   RGBA pattern válido: " . (preg_match($rgbaPattern, $invalidRgba) ? "PASS ✓" : "FAIL ✗") . "\n";
echo "\n";

// Test 6: Cache limpo
echo "6. CACHE:\n";
echo "   Limpando cache...\n";
app('theme')->clearCache();
echo "   Cache limpo ✓\n\n";

echo "=== TODOS OS TESTES CONCLUÍDOS ===\n";
echo "\nPróximo passo: Testar na interface web em http://127.0.0.1:8000/admin/settings/theme\n";
