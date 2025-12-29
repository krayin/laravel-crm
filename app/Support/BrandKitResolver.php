<?php

namespace App\Support;

use App\Models\BrandKitCustomCss;
use App\Models\BrandKitOverride;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BrandKitResolver
{
    private const CACHE_PREFIX = 'brand_kit.resolved';

    private const CACHE_TTL = 3600; // 1h

    private const PRESET_VERSION = 1; // bump se mudar regras/KEY_MAP/TYPE_MAP

    /**
     * theme.json nested path -> flat key interno
     * Suporta tanto formato nested (tokens.primary) quanto flat (color_primary)
     */
    private const KEY_MAP = [
        // tokens -> colors (nested format)
        'tokens.primary'       => 'color_primary',
        'tokens.primary_dark'  => 'color_primary_dark',
        'tokens.primary_light' => 'color_primary_light',
        'tokens.success'       => 'color_success',
        'tokens.warning'       => 'color_warning',
        'tokens.danger'        => 'color_danger',

        // assets (nested format)
        'assets.logo_main'  => 'logo_main',
        'assets.logo_light' => 'logo_light',
        'assets.logo_icon'  => 'logo_icon',
        'assets.favicon'    => 'favicon',

        // login (nested format)
        'login.bg_image'           => 'login_bg_image',
        'login.bg_zoom'            => 'login_bg_zoom',
        'login.bg_opacity'         => 'login_bg_opacity',
        'login.show_powered_by'    => 'login_show_powered_by',
        'login.card_enabled'       => 'login_card_enabled',
        'login.card_bg_image'      => 'login_card_bg_image',
        'login.card_bg_opacity'    => 'login_card_bg_opacity',
        'login.card_overlay_color' => 'login_card_overlay_color',
        'login.card_title'         => 'login_card_title',
        'login.card_subtitle'      => 'login_card_subtitle',
        'login.card_sparkles'      => 'login_card_sparkles',
        'login.card_help_link'     => 'login_card_help_link',
        'login.card_support_email' => 'login_card_support_email',
    ];

    /**
     * Keys que podem existir diretamente no root do theme.json (flat format)
     */
    private const FLAT_KEYS = [
        'color_primary',
        'color_primary_dark',
        'color_primary_light',
        'color_success',
        'color_warning',
        'color_danger',
        'logo_main',
        'logo_light',
        'logo_icon',
        'favicon',
        'login_bg_image',
        'login_bg_zoom',
        'login_bg_opacity',
        'login_show_powered_by',
        'login_card_enabled',
        'login_card_bg_image',
        'login_card_bg_opacity',
        'login_card_overlay_color',
        'login_card_title',
        'login_card_subtitle',
        'login_card_sparkles',
        'login_card_help_link',
        'login_card_support_email',
    ];

    /**
     * Defaults finais (fallback derradeiro)
     */
    private const DEFAULTS = [
        'color_primary'       => '#1E40AF',
        'color_primary_dark'  => '#1E3A8A',
        'color_primary_light' => '#3B82F6',
        'color_success'       => '#10B981',
        'color_warning'       => '#F59E0B',
        'color_danger'        => '#EF4444',

        'logo_main'  => null,
        'logo_light' => null,
        'logo_icon'  => null,
        'favicon'    => null,

        'login_bg_image'           => null,
        'login_bg_zoom'            => 100,
        'login_bg_opacity'         => 50,
        'login_show_powered_by'    => true,
        'login_card_enabled'       => false,
        'login_card_bg_image'      => null,
        'login_card_bg_opacity'    => 62,
        'login_card_overlay_color' => 'rgba(10, 45, 15, 0.78)',
        'login_card_title'         => 'Bem-vindo',
        'login_card_subtitle'      => 'Acesse sua conta para continuar',
        'login_card_sparkles'      => false,
        'login_card_help_link'     => true,
        'login_card_support_email' => 'suporte@empresa.com.br',
    ];

    /**
     * Tipos esperados por chave (para castValue())
     * - string: mantem string
     * - bool: converte "1"/"true"/1 etc
     * - float: converte e normaliza
     * - int: idem
     * - color: normaliza "#RRGGBB" (best effort)
     */
    private const TYPE_MAP = [
        'color_primary'       => 'color',
        'color_primary_dark'  => 'color',
        'color_primary_light' => 'color',
        'color_success'       => 'color',
        'color_warning'       => 'color',
        'color_danger'        => 'color',

        'logo_main'  => 'string',
        'logo_light' => 'string',
        'logo_icon'  => 'string',
        'favicon'    => 'string',

        'login_bg_image'           => 'string',
        'login_bg_zoom'            => 'int',
        'login_bg_opacity'         => 'int',
        'login_show_powered_by'    => 'bool',
        'login_card_enabled'       => 'bool',
        'login_card_bg_image'      => 'string',
        'login_card_bg_opacity'    => 'int',
        'login_card_overlay_color' => 'string',
        'login_card_title'         => 'string',
        'login_card_subtitle'      => 'string',
        'login_card_sparkles'      => 'bool',
        'login_card_help_link'     => 'bool',
        'login_card_support_email' => 'string',
    ];

    public function __construct(private CssValidator $cssValidator) {}

    /**
     * Resolve configuração do tema.
     *
     * @param  string  $scopeKey  Escopo (ex: 'global', tenant ID)
     * @param  string  $themeSlug  Slug do tema
     * @param  bool  $isPreview  Se true, bypassa cache (usado para preview isolado)
     * @param  array|null  $previewOverrides  Overrides adicionais para preview (não persistidos)
     * @param  string|null  $previewCssAdmin  CSS admin para preview
     * @param  string|null  $previewCssLogin  CSS login para preview
     */
    public function resolve(
        string $scopeKey,
        string $themeSlug,
        bool $isPreview = false,
        ?array $previewOverrides = null,
        ?string $previewCssAdmin = null,
        ?string $previewCssLogin = null,
    ): array {
        $scopeKey = $this->sanitizeKey($scopeKey, 'global');
        $themeSlug = $this->sanitizeKey($themeSlug, 'default');

        // Preview: NUNCA cacheia - resolve direto
        if ($isPreview) {
            return $this->resolveUncached(
                $scopeKey,
                $themeSlug,
                true,
                $previewOverrides,
                $previewCssAdmin,
                $previewCssLogin,
            );
        }

        // Normal: usa cache
        $cacheKey = $this->cacheKey($scopeKey, $themeSlug);

        return Cache::remember($cacheKey, self::CACHE_TTL, fn () => $this->resolveUncached($scopeKey, $themeSlug, false, null, null, null)
        );
    }

    /**
     * Resolve configuração sem cache.
     * Usado internamente e para preview.
     */
    private function resolveUncached(
        string $scopeKey,
        string $themeSlug,
        bool $isPreview,
        ?array $previewOverrides,
        ?string $previewCssAdmin,
        ?string $previewCssLogin,
    ): array {
        // 1) preset -> normaliza -> aplica cast
        $presetFlat = $this->loadAndNormalizePreset($themeSlug);

        // 2) theme_configs do ThemeManager (fonte primária de cores/logos)
        $themeManagerConfig = $this->loadThemeManagerConfig();

        // 3) overrides do DB (inclui CLEARs) -> cast
        $overridesRaw = BrandKitOverride::getActiveOverridesWithClears(
            $scopeKey,
            $themeSlug,
        );

        // Separar CLEARs dos valores reais
        $overrides = [];
        $clears = [];

        foreach ($overridesRaw as $key => $value) {
            if (BrandKitOverride::isClear($value)) {
                $clears[] = $key;
            } else {
                $overrides[$key] = $value;
            }
        }

        $overrides = $this->castArray($overrides);

        // 4) Merge seguro: defaults <- preset <- themeManagerConfig <- overrides <- clears
        $config = $this->safeMergeWithClears(
            self::DEFAULTS,
            $presetFlat,
            $themeManagerConfig,
            $overrides,
            $clears,
        );

        // 5) Se preview, aplica overrides de preview POR CIMA de tudo
        if ($isPreview && $previewOverrides !== null) {
            $previewOverrides = $this->castArray($previewOverrides);

            foreach ($previewOverrides as $key => $value) {
                if ($this->isRealValue($value)) {
                    $config[$key] = $value;
                }
            }
        }

        // 6) CSS custom (validado e sanitizado)
        // Em preview, usa CSS de preview se fornecido
        if ($isPreview && $previewCssAdmin !== null) {
            $customCssAdmin = $this->filterCss($previewCssAdmin);
        } else {
            $customCssAdmin = BrandKitCustomCss::getEnabledCss(
                $scopeKey,
                $themeSlug,
                'admin',
            );
            $customCssAdmin = $this->filterCss($customCssAdmin);
        }

        if ($isPreview && $previewCssLogin !== null) {
            $customCssLogin = $this->filterCss($previewCssLogin);
        } else {
            $customCssLogin = BrandKitCustomCss::getEnabledCss(
                $scopeKey,
                $themeSlug,
                'login',
            );
            $customCssLogin = $this->filterCss($customCssLogin);
        }

        return [
            'config'           => $config,
            'custom_css_admin' => $customCssAdmin,
            'custom_css_login' => $customCssLogin,
            'theme_slug'       => $themeSlug,
            'scope_key'        => $scopeKey,
            'preset_version'   => self::PRESET_VERSION,
            'is_preview'       => $isPreview,
        ];
    }

    /**
     * Filtra CSS: valida e sanitiza
     */
    private function filterCss(string $css): string
    {
        if (trim($css) === '') {
            return '';
        }

        if (! $this->cssValidator->isValid($css)) {
            return '';
        }

        return $this->cssValidator->sanitize($css);
    }

    public function invalidate(string $scopeKey, string $themeSlug): void
    {
        $scopeKey = $this->sanitizeKey($scopeKey, 'global');
        $themeSlug = $this->sanitizeKey($themeSlug, 'default');

        Cache::forget($this->cacheKey($scopeKey, $themeSlug));
    }

    /**
     * Invalida "global" para todos os temas disponiveis (fallback seguro sem cache tags)
     */
    public function invalidateAllGlobal(): void
    {
        foreach ($this->getAvailableThemes() as $slug) {
            Cache::forget($this->cacheKey('global', $slug));
        }
    }

    /**
     * Lista temas que possuem theme.json
     */
    public function getAvailableThemes(): array
    {
        $base = storage_path('app/public/themes');

        if (! File::isDirectory($base)) {
            return ['default'];
        }

        $themes = [];

        foreach (File::directories($base) as $dir) {
            $slug = basename($dir);
            $path = $dir.DIRECTORY_SEPARATOR.'theme.json';

            if (File::exists($path)) {
                $themes[] = $slug;
            }
        }

        return empty($themes) ? ['default'] : $themes;
    }

    // =========================
    // Internals
    // =========================

    /**
     * Carrega configurações do ThemeManager (theme_configs table)
     * Esta é a fonte primária de cores/logos definidas pelo usuário no admin.
     */
    private function loadThemeManagerConfig(): array
    {
        try {
            $config = \DB::table('theme_configs')->where('id', 1)->first();

            if (! $config || ! $config->is_active) {
                return [];
            }

            // Mapeia apenas as chaves relevantes para o BrandKit
            $result = [];

            // Cores
            if (! empty($config->color_primary)) {
                $result['color_primary'] = $config->color_primary;
            }
            if (! empty($config->color_primary_dark)) {
                $result['color_primary_dark'] = $config->color_primary_dark;
            }
            if (! empty($config->color_primary_light)) {
                $result['color_primary_light'] = $config->color_primary_light;
            }
            if (! empty($config->color_success)) {
                $result['color_success'] = $config->color_success;
            }
            if (! empty($config->color_warning)) {
                $result['color_warning'] = $config->color_warning;
            }
            if (! empty($config->color_danger)) {
                $result['color_danger'] = $config->color_danger;
            }

            // Logos
            if (! empty($config->logo_main)) {
                $result['logo_main'] = $config->logo_main;
            }
            if (! empty($config->logo_light)) {
                $result['logo_light'] = $config->logo_light;
            }
            if (! empty($config->logo_icon)) {
                $result['logo_icon'] = $config->logo_icon;
            }
            if (! empty($config->favicon)) {
                $result['favicon'] = $config->favicon;
            }

            // Login - todos os campos
            if (! empty($config->login_bg_image)) {
                $result['login_bg_image'] = $config->login_bg_image;
            }
            if (isset($config->login_bg_zoom)) {
                $result['login_bg_zoom'] = (int) $config->login_bg_zoom;
            }
            if (isset($config->login_bg_opacity)) {
                $result['login_bg_opacity'] = (int) $config->login_bg_opacity;
            }
            if (isset($config->login_show_powered_by)) {
                $result['login_show_powered_by'] = (bool) $config->login_show_powered_by;
            }
            if (isset($config->login_card_enabled)) {
                $result['login_card_enabled'] = (bool) $config->login_card_enabled;
            }
            if (! empty($config->login_card_bg_image)) {
                $result['login_card_bg_image'] = $config->login_card_bg_image;
            }
            if (isset($config->login_card_bg_opacity)) {
                $result['login_card_bg_opacity'] = (int) $config->login_card_bg_opacity;
            }
            if (! empty($config->login_card_overlay_color)) {
                $result['login_card_overlay_color'] = $config->login_card_overlay_color;
            }
            if (! empty($config->login_card_title)) {
                $result['login_card_title'] = $config->login_card_title;
            }
            if (! empty($config->login_card_subtitle)) {
                $result['login_card_subtitle'] = $config->login_card_subtitle;
            }
            if (isset($config->login_card_sparkles)) {
                $result['login_card_sparkles'] = (bool) $config->login_card_sparkles;
            }
            if (isset($config->login_card_help_link)) {
                $result['login_card_help_link'] = (bool) $config->login_card_help_link;
            }
            if (! empty($config->login_card_support_email)) {
                $result['login_card_support_email'] = $config->login_card_support_email;
            }

            return $this->castArray($result);
        } catch (\Exception $e) {
            \Log::warning('[BrandKitResolver] Failed to load theme_configs: '.$e->getMessage());

            return [];
        }
    }

    private function loadAndNormalizePreset(string $themeSlug): array
    {
        $path = storage_path("app/public/themes/{$themeSlug}/theme.json");

        if (! File::exists($path)) {
            return [];
        }

        $raw = File::get($path);
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return [];
        }

        $flat = $this->normalizeToFlatKeys($data);

        // cast (principalmente importante para bool/float do JSON)
        return $this->castArray($flat);
    }

    private function normalizeToFlatKeys(array $nested): array
    {
        $flat = [];

        // 1) Primeiro, tenta keys nested (tokens.primary -> color_primary)
        foreach (self::KEY_MAP as $nestedPath => $flatKey) {
            $value = $this->getNestedValue($nested, $nestedPath);

            // So aplica se existir no preset
            if ($value !== null) {
                $flat[$flatKey] = $value;
            }
        }

        // 2) Depois, keys flat que já estão no root (color_primary, login_bg_image, etc.)
        // Estas têm prioridade sobre as nested se existirem
        foreach (self::FLAT_KEYS as $flatKey) {
            if (array_key_exists($flatKey, $nested) && $nested[$flatKey] !== null) {
                $flat[$flatKey] = $nested[$flatKey];
            }
        }

        return $flat;
    }

    private function getNestedValue(array $data, string $path)
    {
        $keys = explode('.', $path);
        $value = $data;

        foreach ($keys as $key) {
            if (! is_array($value) || ! array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    private function safeMerge(
        array $defaults,
        array $preset,
        array $themeManagerConfig,
        array $overrides,
    ): array {
        $result = $defaults;

        // preset (theme.json) entra so com valor real
        foreach ($preset as $key => $value) {
            if ($this->isRealValue($value)) {
                $result[$key] = $value;
            }
        }

        // themeManagerConfig (DB) entra por cima so com valor real
        foreach ($themeManagerConfig as $key => $value) {
            if ($this->isRealValue($value)) {
                $result[$key] = $value;
            }
        }

        // overrides entram por cima so com valor real
        foreach ($overrides as $key => $value) {
            if ($this->isRealValue($value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Merge com suporte a CLEARs.
     *
     * Ordem de precedência (menor → maior):
     * 1. DEFAULTS (constantes)
     * 2. preset (theme.json)
     * 3. themeManagerConfig (theme_configs table)
     * 4. overrides (brand_kit_overrides table)
     * 5. clears (remove valor, volta para DEFAULTS)
     *
     * CLEAR significa: "remova qualquer valor das camadas superiores,
     * volte para o default absoluto (ou null se não houver default)".
     */
    private function safeMergeWithClears(
        array $defaults,
        array $preset,
        array $themeManagerConfig,
        array $overrides,
        array $clears,
    ): array {
        // Primeiro faz o merge normal
        $result = $this->safeMerge($defaults, $preset, $themeManagerConfig, $overrides);

        // Depois aplica CLEARs: volta para o default ou null
        foreach ($clears as $key) {
            if (array_key_exists($key, $defaults)) {
                // Tem default: volta para ele
                $result[$key] = $defaults[$key];
            } else {
                // Sem default: remove completamente
                $result[$key] = null;
            }
        }

        return $result;
    }

    /**
     * Regra de "valor real":
     * - null => nao
     * - string vazia => nao
     * - false e 0 => SIM (sao valores validos!)
     */
    private function isRealValue($value): bool
    {
        if ($value === null) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }

        return true;
    }

    private function castArray(array $data): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            $out[$key] = $this->castValue($key, $value);
        }

        return $out;
    }

    private function castValue(string $key, $value)
    {
        $type = self::TYPE_MAP[$key] ?? 'string';

        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'bool':
                // aceita true/false, 1/0, "1"/"0", "true"/"false"
                if (is_bool($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return ((int) $value) === 1;
                }
                $v = strtolower(trim((string) $value));

                return in_array($v, ['1', 'true', 'yes', 'on'], true);

            case 'int':
                // Converte para int
                if (is_int($value)) {
                    $n = $value;
                } elseif (is_numeric($value)) {
                    $n = (int) $value;
                } else {
                    $n = (int) preg_replace("/[^\d\-]/", '', (string) $value);
                }

                // Clamp: opacity deve ficar entre 0 e 100
                if (in_array($key, ['login_bg_opacity', 'login_card_bg_opacity'], true)) {
                    if ($n < 0) {
                        $n = 0;
                    }
                    if ($n > 100) {
                        $n = 100;
                    }
                }

                // Clamp: zoom deve ficar entre 50 e 150
                if ($key === 'login_bg_zoom') {
                    if ($n < 50) {
                        $n = 50;
                    }
                    if ($n > 150) {
                        $n = 150;
                    }
                }

                return $n;

            case 'float':
                // Mantido para compatibilidade, mas não usado atualmente
                if (is_float($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return (float) $value;
                }
                // tenta converter "0,8" -> "0.8"
                $v = str_replace(',', '.', trim((string) $value));

                return is_numeric($v) ? (float) $v : (self::DEFAULTS[$key] ?? null);

            case 'color':
                return $this->normalizeColor((string) $value);

            case 'string':
            default:
                return (string) $value;
        }
    }

    private function normalizeColor(string $value): string
    {
        $v = trim($value);

        // aceita "0284C7" -> "#0284C7"
        if (preg_match('/^[0-9a-fA-F]{6}$/', $v)) {
            return '#'.strtoupper($v);
        }

        // aceita "#0284C7"
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $v)) {
            return strtoupper($v);
        }

        // fallback: se for invalido, devolve como esta (nao quebra o sistema)
        return $v;
    }

    private function cacheKey(string $scopeKey, string $themeSlug): string
    {
        return self::CACHE_PREFIX.
            '.v'.
            self::PRESET_VERSION.
            ".{$scopeKey}.{$themeSlug}";
    }

    /**
     * Evita path traversal e chaves com caracteres estranhos.
     */
    private function sanitizeKey(string $value, string $fallback): string
    {
        $v = strtolower(trim($value));

        // permite a-z 0-9 _ -
        $v = preg_replace("/[^a-z0-9_\-]/", '', $v);

        return $v !== '' ? $v : $fallback;
    }
}
