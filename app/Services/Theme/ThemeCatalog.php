<?php

namespace App\Services\Theme;

use Illuminate\Support\Facades\File;

/**
 * ThemeCatalog - Fonte única de verdade para catálogo de temas.
 *
 * Este service é upgrade-safe e não depende de código em packages/.
 * Responsável por descobrir e retornar metadados de todos os temas disponíveis.
 */
class ThemeCatalog
{
    /**
     * Cores padrão do sistema (fallback).
     */
    protected const DEFAULT_COLORS = [
        'primary'       => '#1E40AF',
        'primary_dark'  => '#1E3A8A',
        'primary_light' => '#3B82F6',
        'success'       => '#10B981',
        'warning'       => '#F59E0B',
        'danger'        => '#EF4444',
    ];

    /**
     * Caminho base para os temas.
     */
    protected string $themesPath;

    /**
     * Create a new ThemeCatalog instance.
     */
    public function __construct()
    {
        $this->themesPath = storage_path('app/public/themes');
    }

    /**
     * Retorna todos os temas disponíveis com metadados completos.
     *
     * @return array<string, array{
     *     slug: string,
     *     name: string,
     *     version: string,
     *     description: string,
     *     author: string|null,
     *     preview: string|null,
     *     colors: array{
     *         primary: string,
     *         primary_dark: string,
     *         primary_light: string,
     *         success: string,
     *         warning: string,
     *         danger: string
     *     }
     * }>
     */
    public function all(): array
    {
        $themes = [];

        // Sempre incluir tema 'default' primeiro
        $themes['default'] = $this->getDefaultTheme();

        // Descobrir temas em storage/app/public/themes/
        if (! File::isDirectory($this->themesPath)) {
            return $themes;
        }

        $directories = File::directories($this->themesPath);

        foreach ($directories as $directory) {
            $slug = basename($directory);

            // Pular 'default' se existir como pasta (já adicionamos acima)
            if ($slug === 'default') {
                continue;
            }

            $themeData = $this->readThemeJson($directory);

            if ($themeData !== null) {
                $themes[$slug] = $this->normalizeThemeData($slug, $themeData);
            }
        }

        return $themes;
    }

    /**
     * Retorna um tema específico pelo slug.
     */
    public function get(string $slug): ?array
    {
        $all = $this->all();

        return $all[$slug] ?? null;
    }

    /**
     * Verifica se um tema existe.
     */
    public function exists(string $slug): bool
    {
        if ($slug === 'default') {
            return true;
        }

        $themeDir = $this->themesPath.DIRECTORY_SEPARATOR.$slug;
        $themeJson = $themeDir.DIRECTORY_SEPARATOR.'theme.json';

        return File::isDirectory($themeDir) && File::exists($themeJson);
    }

    /**
     * Retorna apenas os slugs dos temas disponíveis.
     *
     * @return array<string>
     */
    public function slugs(): array
    {
        return array_keys($this->all());
    }

    /**
     * Retorna o tema padrão do sistema.
     * Tenta ler do disco primeiro, senão usa valores hardcoded.
     */
    protected function getDefaultTheme(): array
    {
        // Tentar ler do disco primeiro
        $defaultDir = $this->themesPath.DIRECTORY_SEPARATOR.'default';
        $themeData = $this->readThemeJson($defaultDir);

        if ($themeData !== null) {
            return $this->normalizeThemeData('default', $themeData);
        }

        // Fallback hardcoded
        return [
            'slug'        => 'default',
            'name'        => 'Padrão Krayin',
            'version'     => '1.0.0',
            'description' => 'Tema padrão do sistema - visual limpo e moderno',
            'author'      => null,
            'preview'     => null,
            'preview_url' => null,
            'colors'      => self::DEFAULT_COLORS,
        ];
    }

    /**
     * Lê e parseia um arquivo theme.json com validação.
     *
     * @param  string  $themeDirectory  Caminho do diretório do tema
     * @return array|null Dados do tema ou null se inválido
     */
    protected function readThemeJson(string $themeDirectory): ?array
    {
        $themeJsonPath = $themeDirectory.DIRECTORY_SEPARATOR.'theme.json';

        if (! File::exists($themeJsonPath)) {
            return null;
        }

        try {
            $contents = File::get($themeJsonPath);
            $data = json_decode($contents, true);

            // json_decode retorna null em caso de JSON inválido
            if (! is_array($data)) {
                \Log::warning('[ThemeCatalog] Invalid JSON in theme.json', [
                    'path' => $themeJsonPath,
                ]);

                return null;
            }

            return $data;
        } catch (\Exception $e) {
            \Log::warning('[ThemeCatalog] Error reading theme.json', [
                'path'  => $themeJsonPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Normaliza os dados do tema para o formato padrão.
     *
     * @param  array  $themeData  Dados brutos do theme.json
     * @return array Dados normalizados
     */
    protected function normalizeThemeData(string $slug, array $themeData): array
    {
        $preview = $themeData['preview'] ?? null;

        return [
            'slug'        => $slug,
            'name'        => $themeData['name'] ?? ucfirst($slug),
            'version'     => $themeData['version'] ?? '1.0.0',
            'description' => $themeData['description'] ?? '',
            'author'      => $themeData['author'] ?? null,
            'preview'     => $preview,
            'preview_url' => $preview ? '/storage/themes/'.$slug.'/'.$preview : null,
            'colors'      => [
                'primary'       => $themeData['color_primary'] ?? self::DEFAULT_COLORS['primary'],
                'primary_dark'  => $themeData['color_primary_dark'] ?? self::DEFAULT_COLORS['primary_dark'],
                'primary_light' => $themeData['color_primary_light'] ?? self::DEFAULT_COLORS['primary_light'],
                'success'       => $themeData['color_success'] ?? self::DEFAULT_COLORS['success'],
                'warning'       => $themeData['color_warning'] ?? self::DEFAULT_COLORS['warning'],
                'danger'        => $themeData['color_danger'] ?? self::DEFAULT_COLORS['danger'],
            ],
        ];
    }
}
