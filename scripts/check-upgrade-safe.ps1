<#
.SYNOPSIS
    Verifica se o projeto permanece upgrade-safe (sem alteracoes em vendor/packages).

.DESCRIPTION
    Este script falha (exit 1) se houver mudancas staged ou unstaged em:
    - vendor/
    - packages/Webkul/

    Deve ser executado ANTES de commits para garantir que nenhum arquivo
    de pacote foi modificado acidentalmente.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File scripts/check-upgrade-safe.ps1

.NOTES
    Autor: Dev Team
    Data: Janeiro 2026
#>

param(
    [switch]$Verbose,
    [switch]$Fix
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  Upgrade-Safe Check - Krayin CRM          " -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Diretorios proibidos (nao devem ter alteracoes)
$forbiddenPaths = @(
    "vendor/",
    "packages/Webkul/"
)

# Verificar se estamos em um repositorio git
$isGitRepo = git rev-parse --is-inside-work-tree 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Nao e um repositorio git!" -ForegroundColor Red
    exit 1
}

Write-Host "[INFO] Verificando alteracoes em diretorios protegidos..." -ForegroundColor Yellow
Write-Host ""

# Coletar arquivos modificados (staged + unstaged)
$modifiedFiles = @()

# Unstaged changes
$unstagedOutput = git diff --name-only 2>$null
if ($unstagedOutput) {
    $modifiedFiles += $unstagedOutput -split "`n" | Where-Object { $_ -ne "" }
}

# Staged changes
$stagedOutput = git diff --cached --name-only 2>$null
if ($stagedOutput) {
    $modifiedFiles += $stagedOutput -split "`n" | Where-Object { $_ -ne "" }
}

# Remover duplicatas
$modifiedFiles = $modifiedFiles | Select-Object -Unique

if ($Verbose) {
    Write-Host "[DEBUG] Arquivos modificados encontrados: $($modifiedFiles.Count)" -ForegroundColor Gray
}

# Filtrar arquivos em diretorios proibidos
$violations = @()

foreach ($file in $modifiedFiles) {
    foreach ($forbidden in $forbiddenPaths) {
        if ($file -like "$forbidden*") {
            $violations += $file
            break
        }
    }
}

# Resultado
if ($violations.Count -gt 0) {
    Write-Host "[FAIL] Alteracoes detectadas em diretorios protegidos!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Arquivos violando upgrade-safe:" -ForegroundColor Red
    Write-Host "--------------------------------" -ForegroundColor Red

    foreach ($v in $violations) {
        Write-Host "  - $v" -ForegroundColor Yellow
    }

    Write-Host ""
    Write-Host "SOLUCAO:" -ForegroundColor Cyan
    Write-Host "  Para reverter essas alteracoes, execute:" -ForegroundColor White
    Write-Host ""

    foreach ($v in $violations) {
        Write-Host "  git restore -- $v" -ForegroundColor Green
    }

    Write-Host ""
    Write-Host "  Ou para reverter todos de uma vez:" -ForegroundColor White

    $pathsToRestore = ($forbiddenPaths | ForEach-Object { "-- $_" }) -join " "
    Write-Host "  git restore $pathsToRestore" -ForegroundColor Green

    Write-Host ""

    if ($Fix) {
        Write-Host "[FIX] Revertendo alteracoes automaticamente..." -ForegroundColor Yellow
        foreach ($v in $violations) {
            git restore -- $v 2>$null
            if ($LASTEXITCODE -eq 0) {
                Write-Host "  [OK] Revertido: $v" -ForegroundColor Green
            } else {
                Write-Host "  [FAIL] Nao foi possivel reverter: $v" -ForegroundColor Red
            }
        }
        Write-Host ""
        Write-Host "[INFO] Verifique com: git status" -ForegroundColor Cyan
        exit 0
    }

    exit 1
} else {
    Write-Host "[PASS] Nenhuma alteracao em diretorios protegidos!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Diretorios verificados:" -ForegroundColor Gray
    foreach ($p in $forbiddenPaths) {
        Write-Host "  - $p" -ForegroundColor Gray
    }
    Write-Host ""
    Write-Host "O projeto permanece upgrade-safe." -ForegroundColor Green
    exit 0
}
