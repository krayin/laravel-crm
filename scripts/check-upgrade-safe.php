<?php

/**
 * Cross-platform wrapper for upgrade-safe check.
 *
 * Detects OS and runs the appropriate script:
 * - Windows: scripts/check-upgrade-safe.ps1 (via PowerShell)
 * - Linux/macOS: scripts/check-upgrade-safe.sh (via Bash)
 *
 * Usage:
 *   php scripts/check-upgrade-safe.php
 *   php scripts/check-upgrade-safe.php --fix
 *   php scripts/check-upgrade-safe.php --verbose
 */
$baseDir = dirname(__DIR__);
$scriptsDir = __DIR__;

// Parse arguments
$fix = in_array('--fix', $argv) || in_array('-f', $argv);
$verbose = in_array('--verbose', $argv) || in_array('-v', $argv);

// Detect OS
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

if ($isWindows) {
    // Windows: use PowerShell
    $script = $scriptsDir.DIRECTORY_SEPARATOR.'check-upgrade-safe.ps1';

    if (! file_exists($script)) {
        fwrite(STDERR, "[ERROR] Script not found: {$script}\n");
        exit(1);
    }

    $args = [];
    if ($fix) {
        $args[] = '-Fix';
    }
    if ($verbose) {
        $args[] = '-Verbose';
    }

    $argsStr = implode(' ', $args);
    $command = "powershell -ExecutionPolicy Bypass -File \"{$script}\" {$argsStr}";
} else {
    // Linux/macOS: use Bash
    $script = $scriptsDir.DIRECTORY_SEPARATOR.'check-upgrade-safe.sh';

    if (! file_exists($script)) {
        fwrite(STDERR, "[ERROR] Script not found: {$script}\n");
        exit(1);
    }

    // Make executable if not already
    if (! is_executable($script)) {
        chmod($script, 0755);
    }

    $args = [];
    if ($fix) {
        $args[] = '--fix';
    }
    if ($verbose) {
        $args[] = '--verbose';
    }

    $argsStr = implode(' ', $args);
    $command = "\"{$script}\" {$argsStr}";
}

// Change to project root
chdir($baseDir);

// Execute
if ($verbose) {
    echo '[DEBUG] OS: '.PHP_OS."\n";
    echo "[DEBUG] Command: {$command}\n";
    echo "\n";
}

passthru($command, $exitCode);

exit($exitCode);
