<?php

namespace App\Support;

use App\Models\BrandKitCustomCss;
use App\Models\BrandKitOverride;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class BrandKitResolver
{
    private const CACHE_PREFIX = "brand_kit.resolved";
    private const CACHE_TTL = 3600; // 1h
    private const PRESET_VERSION = 1; // bump se mudar regras/KEY_MAP/TYPE_MAP

    /**
     * theme.json nested path -> flat key interno
     */
    private const KEY_MAP = [
        // tokens -> colors
        "tokens.primary" => "color_primary",
        "tokens.primary_dark" => "color_primary_dark",
        "tokens.primary_light" => "color_primary_light",
        "tokens.success" => "color_success",
        "tokens.warning" => "color_warning",
        "tokens.danger" => "color_danger",

        // assets
        "assets.logo_main" => "logo_main",
        "assets.logo_light" => "logo_light",
        "assets.logo_icon" => "logo_icon",
        "assets.favicon" => "favicon",

        // login
        "login.bg_image" => "login_bg_image",
        "login.bg_opacity" => "login_bg_opacity",
        "login.card_enabled" => "login_card_enabled",
        "login.card_title" => "login_card_title",
        "login.card_subtitle" => "login_card_subtitle",
    ];

    /**
     * Defaults finais (fallback derradeiro)
     */
    private const DEFAULTS = [
        "color_primary" => "#0284C7",
        "color_primary_dark" => "#0369A1",
        "color_primary_light" => "#38BDF8",
        "color_success" => "#16A34A",
        "color_warning" => "#D97706",
        "color_danger" => "#DC2626",

        "logo_main" => null,
        "logo_light" => null,
        "logo_icon" => null,
        "favicon" => null,

        "login_bg_image" => null,
        "login_bg_opacity" => 0.8,
        "login_card_enabled" => false,
        "login_card_title" => "",
        "login_card_subtitle" => "",
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
        "color_primary" => "color",
        "color_primary_dark" => "color",
        "color_primary_light" => "color",
        "color_success" => "color",
        "color_warning" => "color",
        "color_danger" => "color",

        "logo_main" => "string",
        "logo_light" => "string",
        "logo_icon" => "string",
        "favicon" => "string",

        "login_bg_image" => "string",
        "login_bg_opacity" => "float",
        "login_card_enabled" => "bool",
        "login_card_title" => "string",
        "login_card_subtitle" => "string",
    ];

    public function __construct(private CssValidator $cssValidator) {}

    public function resolve(string $scopeKey, string $themeSlug): array
    {
        $scopeKey = $this->sanitizeKey($scopeKey, "global");
        $themeSlug = $this->sanitizeKey($themeSlug, "default");

        $cacheKey = $this->cacheKey($scopeKey, $themeSlug);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use (
            $scopeKey,
            $themeSlug,
        ) {
            // 1) preset -> normaliza -> aplica cast
            $presetFlat = $this->loadAndNormalizePreset($themeSlug);

            // 2) overrides do DB (ja vem filtrado null/vazio) -> cast
            $overrides = BrandKitOverride::getActiveOverrides(
                $scopeKey,
                $themeSlug,
            );
            $overrides = $this->castArray($overrides);

            // 3) Merge seguro: defaults <- preset <- overrides
            $config = $this->safeMerge(self::DEFAULTS, $presetFlat, $overrides);

            // 4) CSS custom (validado e sanitizado)
            $customCssAdmin = BrandKitCustomCss::getEnabledCss(
                $scopeKey,
                $themeSlug,
                "admin",
            );
            $customCssLogin = BrandKitCustomCss::getEnabledCss(
                $scopeKey,
                $themeSlug,
                "login",
            );

            $customCssAdmin = $this->filterCss($customCssAdmin);
            $customCssLogin = $this->filterCss($customCssLogin);

            return [
                "config" => $config,
                "custom_css_admin" => $customCssAdmin,
                "custom_css_login" => $customCssLogin,
                "theme_slug" => $themeSlug,
                "scope_key" => $scopeKey,
                "preset_version" => self::PRESET_VERSION,
            ];
        });
    }

    /**
     * Filtra CSS: valida e sanitiza
     */
    private function filterCss(string $css): string
    {
        if (trim($css) === "") {
            return "";
        }

        if (!$this->cssValidator->isValid($css)) {
            return "";
        }

        return $this->cssValidator->sanitize($css);
    }

    public function invalidate(string $scopeKey, string $themeSlug): void
    {
        $scopeKey = $this->sanitizeKey($scopeKey, "global");
        $themeSlug = $this->sanitizeKey($themeSlug, "default");

        Cache::forget($this->cacheKey($scopeKey, $themeSlug));
    }

    /**
     * Invalida "global" para todos os temas disponiveis (fallback seguro sem cache tags)
     */
    public function invalidateAllGlobal(): void
    {
        foreach ($this->getAvailableThemes() as $slug) {
            Cache::forget($this->cacheKey("global", $slug));
        }
    }

    /**
     * Lista temas que possuem theme.json
     */
    public function getAvailableThemes(): array
    {
        $base = storage_path("app/public/themes");

        if (!File::isDirectory($base)) {
            return ["default"];
        }

        $themes = [];

        foreach (File::directories($base) as $dir) {
            $slug = basename($dir);
            $path = $dir . DIRECTORY_SEPARATOR . "theme.json";

            if (File::exists($path)) {
                $themes[] = $slug;
            }
        }

        return empty($themes) ? ["default"] : $themes;
    }

    // =========================
    // Internals
    // =========================

    private function loadAndNormalizePreset(string $themeSlug): array
    {
        $path = storage_path("app/public/themes/{$themeSlug}/theme.json");

        if (!File::exists($path)) {
            return [];
        }

        $raw = File::get($path);
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return [];
        }

        $flat = $this->normalizeToFlatKeys($data);

        // cast (principalmente importante para bool/float do JSON)
        return $this->castArray($flat);
    }

    private function normalizeToFlatKeys(array $nested): array
    {
        $flat = [];

        foreach (self::KEY_MAP as $nestedPath => $flatKey) {
            $value = $this->getNestedValue($nested, $nestedPath);

            // So aplica se existir no preset
            if ($value !== null) {
                $flat[$flatKey] = $value;
            }
        }

        return $flat;
    }

    private function getNestedValue(array $data, string $path)
    {
        $keys = explode(".", $path);
        $value = $data;

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    private function safeMerge(
        array $defaults,
        array $preset,
        array $overrides,
    ): array {
        $result = $defaults;

        // preset entra so com valor real
        foreach ($preset as $key => $value) {
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
        if (is_string($value) && trim($value) === "") {
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
        $type = self::TYPE_MAP[$key] ?? "string";

        if ($value === null) {
            return null;
        }

        switch ($type) {
            case "bool":
                // aceita true/false, 1/0, "1"/"0", "true"/"false"
                if (is_bool($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return ((int) $value) === 1;
                }
                $v = strtolower(trim((string) $value));
                return in_array($v, ["1", "true", "yes", "on"], true);

            case "int":
                if (is_int($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    return (int) $value;
                }
                return (int) preg_replace("/[^\d\-]/", "", (string) $value);

            case "float":
                if (is_float($value)) {
                    return $value;
                }
                if (is_numeric($value)) {
                    $f = (float) $value;

                    // regra especifica: opacity deve ficar entre 0 e 1
                    if ($key === "login_bg_opacity") {
                        if ($f < 0) {
                            $f = 0;
                        }
                        if ($f > 1) {
                            $f = 1;
                        }
                    }

                    return $f;
                }
                // tenta converter "0,8" -> "0.8"
                $v = str_replace(",", ".", trim((string) $value));
                $f = is_numeric($v) ? (float) $v : null;

                if ($key === "login_bg_opacity" && $f !== null) {
                    if ($f < 0) {
                        $f = 0;
                    }
                    if ($f > 1) {
                        $f = 1;
                    }
                }

                return $f ?? (self::DEFAULTS[$key] ?? null);

            case "color":
                return $this->normalizeColor((string) $value);

            case "string":
            default:
                return (string) $value;
        }
    }

    private function normalizeColor(string $value): string
    {
        $v = trim($value);

        // aceita "0284C7" -> "#0284C7"
        if (preg_match('/^[0-9a-fA-F]{6}$/', $v)) {
            return "#" . strtoupper($v);
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
        return self::CACHE_PREFIX .
            ".v" .
            self::PRESET_VERSION .
            ".{$scopeKey}.{$themeSlug}";
    }

    /**
     * Evita path traversal e chaves com caracteres estranhos.
     */
    private function sanitizeKey(string $value, string $fallback): string
    {
        $v = strtolower(trim($value));

        // permite a-z 0-9 _ -
        $v = preg_replace("/[^a-z0-9_\-]/", "", $v);

        return $v !== "" ? $v : $fallback;
    }
}
