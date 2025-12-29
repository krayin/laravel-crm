<?php

namespace App\Support;

use Illuminate\Http\Request;

class ThemeContextFactory
{
    public function __construct(
        private readonly BrandKitResolver $brandKitResolver,
        private readonly ThemeSelectionResolver $themeSelectionResolver,
    ) {}

    public function create(?Request $request = null): ThemeContext
    {
        $scopeKey = $this->resolveScopeKey($request);
        $themeSlug = $this->resolveThemeSlug($request);
        $isPreview = $this->isPreviewMode($request);

        // BrandKit: preset + overrides + css
        $brandKit = $this->brandKitResolver->resolve($scopeKey, $themeSlug);

        // Config final (nesta fase: BrandKit e a fonte principal)
        $config = $brandKit['config'] ?? [];

        // LoginConfig: filtra chaves login_ e remove o prefixo
        $loginConfig = $this->extractLoginConfig($config);

        return new ThemeContext(
            enabled: true,
            slug: $themeSlug,
            scopeKey: $scopeKey,
            config: $config,
            loginConfig: $loginConfig,
            isPreview: $isPreview,
            customCssAdmin: (string) ($brandKit['custom_css_admin'] ?? ''),
            customCssLogin: (string) ($brandKit['custom_css_login'] ?? ''),
        );
    }

    private function resolveScopeKey(?Request $request): string
    {
        // Multi-tenant future-ready (por enquanto global)
        return 'global';
    }

    private function resolveThemeSlug(?Request $request): string
    {
        // Preview tem prioridade (por sessao)
        if ($request && session()->has('theme_preview')) {
            return $this->sanitizeSlug((string) session('theme_preview'));
        }

        // Tema persistido (DB/config)
        return $this->sanitizeSlug(
            $this->themeSelectionResolver->getSelectedThemeSlug(),
        ) ?:
            'default';
    }

    private function isPreviewMode(?Request $request): bool
    {
        return (bool) ($request && session()->has('theme_preview'));
    }

    /**
     * Converte config keys:
     * login_bg_image -> ['bg_image' => ...]
     * login_card_enabled -> ['card_enabled' => ...]
     */
    private function extractLoginConfig(array $config): array
    {
        $out = [];

        foreach ($config as $key => $value) {
            if (is_string($key) && str_starts_with($key, 'login_')) {
                $out[substr($key, 6)] = $value;
            }
        }

        return $out;
    }

    private function sanitizeSlug(string $value): string
    {
        $v = strtolower(trim($value));
        $v = preg_replace("/[^a-z0-9_\-]/", '', $v);

        return $v ?: 'default';
    }
}
