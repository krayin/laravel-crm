<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Resolver determinístico de configurações de tema.
 *
 * Responsabilidades:
 * - Resolver configurações com precedência clara (DB > theme.json > defaults)
 * - Validar existência de temas
 * - Ler e parsear theme.json
 * - NÃO ter side-effects (não escreve no DB)
 *
 * Precedência:
 * 1. Se is_active=1 E campo no DB não é null/empty → usa valor do DB
 * 2. Senão → usa valor do theme.json do tema selecionado
 * 3. Se theme.json não existe → usa defaults
 */
final class ThemeConfigResolver
{
    /**
     * Pasta base dos temas no storage.
     */
    private const THEMES_PATH = 'themes';

    /**
     * Slug do tema padrão (fallback).
     */
    public const DEFAULT_SLUG = 'default';

    /**
     * Configurações padrão (fallback final).
     */
    private const DEFAULTS = [
        'color_primary' => '#1E40AF',
        'color_primary_dark' => '#1E3A8A',
        'color_primary_light' => '#3B82F6',
        'color_success' => '#10B981',
        'color_warning' => '#F59E0B',
        'color_danger' => '#EF4444',
        'logo_main' => null,
        'logo_light' => null,
        'logo_icon' => null,
        'favicon' => null,
    ];

    /**
     * Configurações padrão de login.
     */
    private const LOGIN_DEFAULTS = [
        'bg_image' => null,
        'bg_zoom' => 100,
        'bg_opacity' => 50,
        'show_powered_by' => true,
        'card_enabled' => false,
        'card_bg_image' => null,
        'card_bg_opacity' => 62,
        'card_overlay_color' => 'rgba(10, 45, 15, 0.78)',
        'card_title' => 'Bem-vindo',
        'card_subtitle' => 'Acesse sua conta para continuar',
        'card_sparkles' => false,
        'card_help_link' => true,
        'card_support_email' => 'suporte@empresa.com.br',
    ];

    /**
     * Busca configuração do banco de dados.
     * Retorna null se não existir.
     */
    public static function getDbConfig(): ?object
    {
        return ThemeCache::rememberConfig(function () {
            return DB::table('theme_configs')
                ->where('id', 1)
                ->first();
        });
    }

    /**
     * Verifica se o sistema de temas está ativo.
     */
    public static function isActive(): bool
    {
        $config = self::getDbConfig();

        return $config && (bool) ($config->is_active ?? false);
    }

    /**
     * Resolve o slug do tema a ser usado.
     *
     * @param string|null $overrideSlug Slug de override (preview)
     * @return string Slug validado do tema
     */
    public static function resolveSlug(?string $overrideSlug = null): string
    {
        // 1. Se há override (preview), usa ele
        if ($overrideSlug !== null) {
            $sanitized = self::sanitizeSlug($overrideSlug);

            if (self::themeExists($sanitized)) {
                return $sanitized;
            }

            Log::warning('[Theme] Preview theme not found, using default', [
                'requested' => $overrideSlug,
                'sanitized' => $sanitized,
            ]);

            return self::DEFAULT_SLUG;
        }

        // 2. Busca do banco
        $config = self::getDbConfig();
        $dbSlug = $config->selected_theme ?? self::DEFAULT_SLUG;
        $sanitized = self::sanitizeSlug($dbSlug);

        // 3. Valida existência
        if ($sanitized !== self::DEFAULT_SLUG && !self::themeExists($sanitized)) {
            Log::warning('[Theme] Selected theme not found', [
                'selected' => $sanitized,
                'fallback' => self::DEFAULT_SLUG,
            ]);

            // NÃO atualiza DB aqui - apenas loga e retorna fallback
            return self::DEFAULT_SLUG;
        }

        return $sanitized;
    }

    /**
     * Resolve configurações gerais com precedência.
     *
     * Precedência:
     * 1. DB (se is_active=1 e valor não null/empty)
     * 2. theme.json
     * 3. defaults
     *
     * @param string $slug Slug do tema
     * @return array Configurações resolvidas
     */
    public static function resolveConfig(string $slug): array
    {
        $dbConfig = self::getDbConfig();
        $themeJson = self::readThemeJson($slug);
        $isActive = (bool) ($dbConfig->is_active ?? false);

        $resolved = [];

        foreach (self::DEFAULTS as $key => $default) {
            $resolved[$key] = self::resolveValue(
                key: $key,
                dbConfig: $dbConfig,
                themeJson: $themeJson,
                default: $default,
                isActive: $isActive
            );
        }

        return $resolved;
    }

    /**
     * Resolve configurações de login com precedência.
     *
     * @param string $slug Slug do tema
     * @return array Configurações de login resolvidas
     */
    public static function resolveLoginConfig(string $slug): array
    {
        $dbConfig = self::getDbConfig();
        $themeJson = self::readThemeJson($slug);
        $isActive = (bool) ($dbConfig->is_active ?? false);

        $resolved = [];

        foreach (self::LOGIN_DEFAULTS as $key => $default) {
            // Mapeia keys de login (prefixo login_ no DB)
            $dbKey = 'login_' . $key;

            $resolved[$key] = self::resolveValue(
                key: $dbKey,
                dbConfig: $dbConfig,
                themeJson: $themeJson['login'] ?? [],
                default: $default,
                isActive: $isActive,
                themeJsonKey: $key // Key no theme.json é sem prefixo
            );
        }

        return $resolved;
    }

    /**
     * Resolve um valor único com precedência.
     *
     * @param string $key Chave no DB
     * @param object|null $dbConfig Configuração do DB
     * @param array $themeJson Dados do theme.json
     * @param mixed $default Valor padrão
     * @param bool $isActive Se o tema está ativo
     * @param string|null $themeJsonKey Chave alternativa para theme.json
     * @return mixed Valor resolvido
     */
    private static function resolveValue(
        string $key,
        ?object $dbConfig,
        array $themeJson,
        mixed $default,
        bool $isActive,
        ?string $themeJsonKey = null
    ): mixed {
        $jsonKey = $themeJsonKey ?? $key;

        // 1. Se is_active=1 e DB tem valor não vazio, usa DB
        if ($isActive && $dbConfig !== null) {
            $dbValue = $dbConfig->$key ?? null;

            if ($dbValue !== null && $dbValue !== '') {
                return $dbValue;
            }
        }

        // 2. Se theme.json tem valor, usa ele
        if (isset($themeJson[$jsonKey]) && $themeJson[$jsonKey] !== null && $themeJson[$jsonKey] !== '') {
            return $themeJson[$jsonKey];
        }

        // 3. Fallback para default
        return $default;
    }

    /**
     * Verifica se um tema existe.
     *
     * @param string $slug Slug do tema
     * @return bool True se existe
     */
    public static function themeExists(string $slug): bool
    {
        // Tema 'default' sempre existe (virtual)
        if ($slug === self::DEFAULT_SLUG) {
            return true;
        }

        return Storage::disk('public')->exists(
            self::THEMES_PATH . '/' . $slug . '/theme.json'
        );
    }

    /**
     * Lê e parseia o theme.json de um tema.
     *
     * @param string $slug Slug do tema
     * @return array Dados do theme.json ou array vazio
     */
    public static function readThemeJson(string $slug): array
    {
        if ($slug === self::DEFAULT_SLUG) {
            return [];
        }

        $path = self::THEMES_PATH . '/' . $slug . '/theme.json';

        if (!Storage::disk('public')->exists($path)) {
            return [];
        }

        try {
            $contents = Storage::disk('public')->get($path);
            $data = json_decode($contents, true);

            if (!is_array($data)) {
                Log::warning('[Theme] Invalid theme.json', ['slug' => $slug]);
                return [];
            }

            return $data;
        } catch (\Throwable $e) {
            Log::warning('[Theme] Error reading theme.json', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Sanitiza o slug do tema.
     *
     * @param string $value Valor a sanitizar
     * @return string Slug sanitizado
     */
    public static function sanitizeSlug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-_]/', '', $value) ?? '';
        $value = trim($value, '-_');

        if ($value === '') {
            return self::DEFAULT_SLUG;
        }

        return substr($value, 0, 50);
    }

    /**
     * Retorna os defaults de configuração geral.
     */
    public static function getDefaults(): array
    {
        return self::DEFAULTS;
    }

    /**
     * Retorna os defaults de configuração de login.
     */
    public static function getLoginDefaults(): array
    {
        return self::LOGIN_DEFAULTS;
    }
}
