<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * ThemePreviewGenerateCommand - Gera preview.svg placeholder para temas.
 *
 * Uso:
 *   php artisan theme:preview:generate --all           # Todos os temas
 *   php artisan theme:preview:generate --slug=default  # Tema específico
 *   php artisan theme:preview:generate --all --force   # Sobrescrever existentes
 *   php artisan theme:preview:generate --all --quiet   # Sem output
 *
 * Seguro para produção: não baixa nada da internet, gera SVG local.
 * Upgrade-safe: só escreve em storage/app/public/themes/.
 */
class ThemePreviewGenerateCommand extends Command
{
    protected $signature = 'theme:preview:generate
                            {--all : Gerar para todos os temas}
                            {--slug= : Slug do tema específico}
                            {--force : Sobrescrever preview existente}';

    protected $description = 'Gera preview.svg placeholder para temas sem preview';

    private int $generated = 0;

    private int $skipped = 0;

    private int $updated = 0;

    public function handle(): int
    {
        $all = $this->option('all');
        $slug = $this->option('slug');
        $force = $this->option('force');
        $quiet = $this->option('quiet');

        if (! $all && ! $slug) {
            $this->error('Especifique --all ou --slug=<slug>');

            return 1;
        }

        $themesPath = storage_path('app/public/themes');

        if (! File::isDirectory($themesPath)) {
            $this->error("Diretório de temas não existe: {$themesPath}");

            return 1;
        }

        if (! $quiet) {
            $this->newLine();
            $this->line('╔══════════════════════════════════════════════════════╗');
            $this->line('║       Theme Preview Generator                        ║');
            $this->line('╚══════════════════════════════════════════════════════╝');
            $this->newLine();
        }

        if ($all) {
            $themeDirs = File::directories($themesPath);

            foreach ($themeDirs as $dir) {
                $themeSlug = basename($dir);
                $this->processTheme($themeSlug, $dir, $force, $quiet);
            }
        } else {
            $themeDir = $themesPath.DIRECTORY_SEPARATOR.$slug;

            if (! File::isDirectory($themeDir)) {
                $this->error("Tema não encontrado: {$slug}");

                return 1;
            }

            $this->processTheme($slug, $themeDir, $force, $quiet);
        }

        if (! $quiet) {
            $this->newLine();
            $this->info('▶ RESUMO');
            $this->line("  Gerados:     {$this->generated}");
            $this->line("  Atualizados: {$this->updated}");
            $this->line("  Pulados:     {$this->skipped}");
            $this->newLine();

            if ($this->generated > 0 || $this->updated > 0) {
                $this->info('Execute "php artisan theme:doctor" para verificar.');
            }
        }

        return 0;
    }

    /**
     * Processa um tema: gera preview e atualiza theme.json se necessário.
     */
    private function processTheme(string $slug, string $themeDir, bool $force, bool $quiet): void
    {
        $previewPath = $themeDir.DIRECTORY_SEPARATOR.'preview.svg';
        $jsonPath = $themeDir.DIRECTORY_SEPARATOR.'theme.json';

        // Ler theme.json para pegar cores
        $themeData = [];
        if (File::exists($jsonPath)) {
            $themeData = json_decode(File::get($jsonPath), true) ?? [];
        }

        $previewExists = File::exists($previewPath);

        // Verificar se deve pular
        if ($previewExists && ! $force) {
            if (! $quiet) {
                $this->line("  [SKIP] {$slug}: preview já existe");
            }
            $this->skipped++;

            return;
        }

        // Extrair cores do tema
        $colorPrimary = $themeData['color_primary'] ?? '#1E40AF';
        $colorPrimaryDark = $themeData['color_primary_dark'] ?? $this->darkenColor($colorPrimary);
        $colorPrimaryLight = $themeData['color_primary_light'] ?? $this->lightenColor($colorPrimary);
        $colorSuccess = $themeData['color_success'] ?? '#10B981';
        $colorWarning = $themeData['color_warning'] ?? '#F59E0B';
        $colorDanger = $themeData['color_danger'] ?? '#EF4444';
        $themeName = $themeData['name'] ?? ucfirst(str_replace('-', ' ', $slug));

        // Gerar SVG
        $svg = $this->generatePreviewSvg(
            $slug,
            $themeName,
            $colorPrimary,
            $colorPrimaryDark,
            $colorPrimaryLight,
            $colorSuccess,
            $colorWarning,
            $colorDanger
        );

        // Salvar preview.svg
        File::put($previewPath, $svg);

        if ($previewExists) {
            if (! $quiet) {
                $this->line("  <fg=yellow>[UPDATE]</> {$slug}: preview regenerado");
            }
            $this->updated++;
        } else {
            if (! $quiet) {
                $this->line("  <fg=green>[OK]</> {$slug}: preview gerado");
            }
            $this->generated++;
        }

        // Atualizar theme.json se não tem "preview"
        if (! isset($themeData['preview']) || $themeData['preview'] !== 'preview.svg') {
            $themeData['preview'] = 'preview.svg';
            File::put($jsonPath, json_encode($themeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            if (! $quiet) {
                $this->line("        theme.json atualizado com preview: preview.svg");
            }
        }
    }

    /**
     * Gera o conteúdo SVG do preview.
     */
    private function generatePreviewSvg(
        string $slug,
        string $name,
        string $primary,
        string $primaryDark,
        string $primaryLight,
        string $success,
        string $warning,
        string $danger
    ): string {
        // Gerar um padrão visual baseado no slug
        $patternId = 'pattern_'.md5($slug);
        $gradientId = 'grad_'.md5($slug);

        // Card background color (claro)
        $cardBg = $this->lightenColor($primary, 0.9);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
  <defs>
    <linearGradient id="{$gradientId}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$primary};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$primaryLight};stop-opacity:1" />
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="400" height="300" fill="url(#{$gradientId})"/>

  <!-- Header bar -->
  <rect x="0" y="0" width="400" height="50" fill="{$primaryDark}" opacity="0.8"/>

  <!-- Logo placeholder -->
  <circle cx="30" cy="25" r="15" fill="white" opacity="0.9"/>

  <!-- Nav items -->
  <rect x="60" y="20" width="40" height="10" rx="2" fill="white" opacity="0.6"/>
  <rect x="110" y="20" width="40" height="10" rx="2" fill="white" opacity="0.6"/>
  <rect x="160" y="20" width="40" height="10" rx="2" fill="white" opacity="0.6"/>

  <!-- Sidebar -->
  <rect x="0" y="50" width="80" height="250" fill="{$primaryDark}" opacity="0.5"/>

  <!-- Sidebar items -->
  <rect x="10" y="70" width="60" height="8" rx="2" fill="white" opacity="0.4"/>
  <rect x="10" y="90" width="50" height="8" rx="2" fill="white" opacity="0.4"/>
  <rect x="10" y="110" width="55" height="8" rx="2" fill="white" opacity="0.4"/>
  <rect x="10" y="130" width="45" height="8" rx="2" fill="white" opacity="0.4"/>

  <!-- Main content area -->
  <rect x="100" y="70" width="280" height="200" rx="8" fill="white" opacity="0.95"/>

  <!-- Content header -->
  <rect x="120" y="90" width="120" height="12" rx="2" fill="{$primary}"/>

  <!-- Content cards -->
  <rect x="120" y="120" width="110" height="60" rx="4" fill="{$cardBg}"/>
  <rect x="250" y="120" width="110" height="60" rx="4" fill="{$cardBg}"/>
  <rect x="120" y="195" width="240" height="50" rx="4" fill="{$cardBg}"/>

  <!-- Color circles (theme palette) -->
  <circle cx="130" cy="140" r="8" fill="{$primary}"/>
  <circle cx="150" cy="140" r="8" fill="{$success}"/>
  <circle cx="170" cy="140" r="8" fill="{$warning}"/>
  <circle cx="190" cy="140" r="8" fill="{$danger}"/>

  <!-- Theme name -->
  <text x="200" y="285" font-family="Arial, sans-serif" font-size="14" fill="white" text-anchor="middle" font-weight="bold">{$name}</text>
</svg>
SVG;
    }

    /**
     * Escurece uma cor hex.
     */
    private function darkenColor(string $hex, float $factor = 0.2): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = max(0, hexdec(substr($hex, 0, 2)) * (1 - $factor));
        $g = max(0, hexdec(substr($hex, 2, 2)) * (1 - $factor));
        $b = max(0, hexdec(substr($hex, 4, 2)) * (1 - $factor));

        return sprintf('#%02X%02X%02X', (int) $r, (int) $g, (int) $b);
    }

    /**
     * Clareia uma cor hex.
     */
    private function lightenColor(string $hex, float $factor = 0.2): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = min(255, hexdec(substr($hex, 0, 2)) + (255 - hexdec(substr($hex, 0, 2))) * $factor);
        $g = min(255, hexdec(substr($hex, 2, 2)) + (255 - hexdec(substr($hex, 2, 2))) * $factor);
        $b = min(255, hexdec(substr($hex, 4, 2)) + (255 - hexdec(substr($hex, 4, 2))) * $factor);

        return sprintf('#%02X%02X%02X', (int) $r, (int) $g, (int) $b);
    }
}
