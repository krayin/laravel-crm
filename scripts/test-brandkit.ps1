# =============================================================================
# BrandKit Test Suite Runner
# =============================================================================
# Roda todos os testes do sistema de temas/BrandKit em isolamento total.
#
# Uso:
#   .\scripts\test-brandkit.ps1           # Roda suite completa (Pest)
#   .\scripts\test-brandkit.ps1 -Coverage # Com cobertura de codigo
#   .\scripts\test-brandkit.ps1 -Filter "Preview"  # Filtra por nome
#   .\scripts\test-brandkit.ps1 -PHPUnit  # Usa PHPUnit em vez de Pest
#
# Caracteristicas:
#   - SQLite :memory: (nao depende das migrations do CRM)
#   - Array cache (sem file cache)
#   - ~10-15 segundos para 77 testes
# =============================================================================

param(
    [switch]$Coverage,
    [switch]$PHPUnit,
    [string]$Filter = ""
)

$ErrorActionPreference = "Stop"

# Vai para a raiz do projeto
Set-Location $PSScriptRoot\..

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host " BrandKit Test Suite" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

if ($PHPUnit) {
    # Modo PHPUnit
    $cmd = "php vendor/bin/phpunit"

    $testFiles = @(
        "tests/Unit/BrandKitResolverTest.php",
        "tests/Unit/BrandKitSnapshotServiceTest.php",
        "tests/Unit/BrandKitCacheInvalidatorTest.php",
        "tests/Unit/PreviewSessionTest.php",
        "tests/Unit/ThemeContextFactoryTest.php"
    )

    $cmd += " " + ($testFiles -join " ")

    if (-not $Coverage) {
        $cmd += " --no-coverage"
    }

    if ($Filter -ne "") {
        $cmd += " --filter `"$Filter`""
    }
} else {
    # Modo Pest (default)
    $cmd = "php vendor/bin/pest"

    $defaultFilter = "BrandKit|PreviewSession|ThemeContextFactory"

    if ($Filter -ne "") {
        $cmd += " --filter `"$Filter`""
    } else {
        $cmd += " --filter `"$defaultFilter`""
    }

    if ($Coverage) {
        $cmd += " --coverage"
    }
}

Write-Host "Executando: $cmd`n" -ForegroundColor DarkGray

# Executa
Invoke-Expression $cmd

$exitCode = $LASTEXITCODE

Write-Host "`n========================================" -ForegroundColor Cyan
if ($exitCode -eq 0) {
    Write-Host " SUCESSO - Todos os testes passaram" -ForegroundColor Green
} else {
    Write-Host " FALHA - Alguns testes falharam" -ForegroundColor Red
}
Write-Host "========================================`n" -ForegroundColor Cyan

exit $exitCode
