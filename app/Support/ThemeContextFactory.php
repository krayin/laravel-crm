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
        // Limpa preview expirado automaticamente
        PreviewSession::clearIfExpired();

        $isPreview = $this->isPreviewMode($request);
        $scopeKey = $this->resolveScopeKey($request);
        $themeSlug = $this->resolveThemeSlug($request);

        // BrandKit: preset + overrides + css
        // Se preview ativo, usa dados da session (bypass cache)
        if ($isPreview && PreviewSession::isActive()) {
            $previewData = PreviewSession::all();

            $brandKit = $this->brandKitResolver->resolve(
                $previewData['scope_key'] ?: $scopeKey,
                $previewData['theme_slug'] ?: $themeSlug,
                true, // isPreview = true (bypass cache)
                $previewData['overrides'] ?: null,
                $previewData['css_admin'] ?: null,
                $previewData['css_login'] ?: null,
            );
        } else {
            // Normal: usa cache
            $brandKit = $this->brandKitResolver->resolve($scopeKey, $themeSlug);
        }

        // Config final (nesta fase: BrandKit e a fonte principal)
        $config = $brandKit['config'] ?? [];

        // LoginConfig: filtra chaves login_ e remove o prefixo
        $loginConfig = $this->extractLoginConfig($config);

        return new ThemeContext(
            enabled: true,
            slug: $brandKit['theme_slug'] ?? $themeSlug,
            scopeKey: $brandKit['scope_key'] ?? $scopeKey,
            config: $config,
            loginConfig: $loginConfig,
            isPreview: $isPreview,
            customCssAdmin: (string) ($brandKit['custom_css_admin'] ?? ''),
            customCssLogin: (string) ($brandKit['custom_css_login'] ?? ''),
        );
    }

    private function resolveScopeKey(?Request $request): string
    {
        // Se preview ativo, usa scope do preview
        if (PreviewSession::isActive()) {
            return PreviewSession::getScopeKey();
        }

        // Multi-tenant future-ready (por enquanto global)
        return 'global';
    }

    private function resolveThemeSlug(?Request $request): string
    {
        // Preview da nova session tem prioridade
        if (PreviewSession::isActive()) {
            return $this->sanitizeSlug(PreviewSession::getThemeSlug());
        }

        // Preview legado (compatibilidade) - via session simples
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
        // Nova session de preview
        if (PreviewSession::isActive()) {
            return true;
        }

        // Legado (compatibilidade)
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
