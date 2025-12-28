<?php

namespace App\Support;

use App\Http\Middleware\HandleThemePreview;

/**
 * Resolve o theme_slug correto considerando:
 * 1) Preview via session (prioridade)
 * 2) Tema selecionado no DB (theme_configs.selected_theme)
 * 3) Fallback 'default'
 *
 * Uso principal: BrandKit controllers quando request nao envia theme_slug
 */
final class ThemeSlugResolver
{
    public function __construct(
        private readonly ThemeSelectionResolver $themeSelectionResolver,
    ) {}

    /**
     * Resolve o theme_slug atual.
     *
     * Prioridade:
     * 1. Preview ativo (session)
     * 2. Tema selecionado no DB
     * 3. Fallback 'default'
     */
    public function resolve(): string
    {
        // 1) Preview via session tem prioridade
        $preview = HandleThemePreview::getActivePreview();
        if ($preview !== null && $preview !== '') {
            return $this->sanitize($preview);
        }

        // 2) Tema selecionado no DB
        $selected = $this->themeSelectionResolver->getSelectedThemeSlug();
        if ($selected !== '' && $selected !== 'default') {
            return $selected; // ja sanitizado pelo ThemeSelectionResolver
        }

        // 3) Fallback
        return 'default';
    }

    /**
     * Resolve considerando um valor opcional do request.
     * Se o request trouxer theme_slug valido, usa ele.
     * Senao, resolve automaticamente.
     */
    public function resolveFromRequest(?string $requestThemeSlug): string
    {
        if ($requestThemeSlug !== null && $requestThemeSlug !== '') {
            return $this->sanitize($requestThemeSlug);
        }

        return $this->resolve();
    }

    private function sanitize(string $value): string
    {
        $v = strtolower(trim($value));
        $v = preg_replace('/[^a-z0-9_\-]/', '', $v);
        return $v ?: 'default';
    }
}
