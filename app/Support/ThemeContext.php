<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Value Object imutavel: Views so leem propriedades/metodos.
 * Zero queries em Blade.
 */
final class ThemeContext
{
    public function __construct(
        public readonly bool $enabled,
        public readonly string $slug,
        public readonly string $scopeKey,
        public readonly array $config,
        public readonly array $loginConfig,
        public readonly bool $isPreview,
        public readonly string $customCssAdmin,
        public readonly string $customCssLogin,
    ) {}

    /**
     * Factory para contexto desabilitado (fallback seguro)
     */
    public static function disabled(): self
    {
        return new self(
            enabled: false,
            slug: 'default',
            scopeKey: 'global',
            config: [],
            loginConfig: [],
            isPreview: false,
            customCssAdmin: '',
            customCssLogin: '',
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Acesso aos itens de login (ja normalizados / filtrados)
     */
    public function login(string $key, mixed $default = null): mixed
    {
        return $this->loginConfig[$key] ?? $default;
    }

    /**
     * Variaveis CSS para injecao no <head>.
     */
    public function cssVariables(): string
    {
        $vars = [
            '--color-primary'       => $this->get('color_primary'),
            '--color-primary-dark'  => $this->get('color_primary_dark'),
            '--color-primary-light' => $this->get('color_primary_light'),
            '--color-success'       => $this->get('color_success'),
            '--color-warning'       => $this->get('color_warning'),
            '--color-danger'        => $this->get('color_danger'),
        ];

        $css = ':root {';
        foreach ($vars as $name => $value) {
            if ($value !== null && $value !== '') {
                $css .= " {$name}: {$value};";
            }
        }
        $css .= ' }';

        return $css;
    }

    /**
     * Classes globais do body (compat)
     */
    public function bodyClasses(): string
    {
        if (! $this->enabled) {
            return '';
        }

        $classes = [
            'theme-enabled',
            'theme-'.$this->slug,
            $this->isPreview ? 'theme-preview' : null,
        ];

        // Background de login
        if ($this->login('bg_image')) {
            $classes[] = 'theme-login-bg';
        }

        // Card customizado
        if ($this->hasCustomCard()) {
            $classes[] = 'theme-login-card-custom';
        }

        return trim(implode(' ', array_filter($classes)));
    }

    /**
     * URL do background do login (compat)
     */
    public function loginBgUrl(): ?string
    {
        // suportar tanto login_bg_image quanto login_bg_url
        $bg = $this->get('login_bg_image') ?? $this->get('login_bg_url');

        return $this->resolveAssetUrl($bg, "storage/themes/{$this->slug}");
    }

    /**
     * Exibe card custom no login (compat)
     */
    public function hasCustomCard(): bool
    {
        // seu plano usa login_card_enabled
        $val = $this->get('login_card_enabled', false);

        // garantir bool mesmo se vier string
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Background do card de login (compat)
     */
    public function loginCardBgUrl(): ?string
    {
        // Nem todo theme.json tem isso; deixamos opcional e seguro
        $bg =
            $this->get('login_card_bg_image') ??
            $this->get('login_card_bg_url');

        return $this->resolveAssetUrl($bg, "storage/themes/{$this->slug}");
    }

    /**
     * URL de um CSS adicional do tema (compat)
     */
    public function cssUrl(): ?string
    {
        // 1) se existir key explicita no config, usa ela
        $css = $this->get('theme_css') ?? $this->get('css');

        $url = $this->resolveAssetUrl($css, "storage/themes/{$this->slug}");
        if ($url) {
            return $url;
        }

        // 2) fallback: procurar theme.css no storage do tema
        $candidate = public_path("storage/themes/{$this->slug}/theme.css");
        if (is_file($candidate)) {
            return asset("storage/themes/{$this->slug}/theme.css");
        }

        return null;
    }

    /**
     * Helper: resolve url segura para assets do tema
     */
    private function resolveAssetUrl(
        ?string $value,
        string $basePublicPath,
    ): ?string {
        if (! $value) {
            return null;
        }

        $value = trim($value);

        // URL absoluta
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        // Path absoluto no site
        if (Str::startsWith($value, ['/'])) {
            return url($value);
        }

        // Arquivo relativo ao tema
        return asset($basePublicPath.'/'.ltrim($value, '/'));
    }

    /**
     * Helpers de assets do tema
     */
    public function themeAsset(?string $relativePath): ?string
    {
        if (! $relativePath) {
            return null;
        }

        return asset("storage/themes/{$this->slug}/{$relativePath}");
    }

    /**
     * Get logo by type: main, light, icon
     * Used by views: $themeContext->logo('main')
     */
    public function logo(string $type = 'main'): ?string
    {
        $key = match ($type) {
            'main'  => 'logo_main',
            'light' => 'logo_light',
            'icon'  => 'logo_icon',
            default => "logo_{$type}",
        };

        $value = $this->get($key);

        if (! $value) {
            return null;
        }

        // Se já é URL completa, retorna direto
        if (
            str_starts_with($value, 'http://') ||
            str_starts_with($value, 'https://') ||
            str_starts_with($value, '/')
        ) {
            return $value;
        }

        // Se é path relativo, monta URL do storage
        return asset("storage/theme-manager/{$value}");
    }

    public function logoMain(): ?string
    {
        return $this->logo('main');
    }

    public function logoLight(): ?string
    {
        return $this->logo('light');
    }

    public function logoIcon(): ?string
    {
        return $this->logo('icon');
    }

    public function favicon(): ?string
    {
        $value = $this->get('favicon');

        if (! $value) {
            return null;
        }

        if (
            str_starts_with($value, 'http://') ||
            str_starts_with($value, 'https://') ||
            str_starts_with($value, '/')
        ) {
            return $value;
        }

        return asset("storage/theme-manager/{$value}");
    }

    /**
     * Whether to show "Powered by Krayin/Webkul" footer
     * Default: true (show it)
     */
    public function showPoweredBy(): bool
    {
        $value = $this->get('show_powered_by', true);

        // Handle various truthy/falsy values
        if (is_bool($value)) {
            return $value;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE,
        ) ?? true;
    }

    /**
     * Check if currently in preview mode
     * Alias for isPreview property for view compatibility
     */
    public function inPreviewMode(): bool
    {
        return $this->isPreview;
    }
}
