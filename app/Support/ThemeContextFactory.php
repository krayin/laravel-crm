<?php

namespace App\Support;

use App\Http\Middleware\HandleThemePreview;
use Illuminate\Support\Facades\Log;

/**
 * Factory para criar instâncias de ThemeContext.
 *
 * REFATORADO para ser:
 * - Determinístico: mesmos inputs = mesmos outputs
 * - Testável: sem dependências ocultas
 * - Cache robusto: usa ThemeCache centralizado
 * - Sem side-effects: não escreve no DB
 *
 * FLUXO:
 * 1. Verifica se há preview ativo na sessão (bypass cache)
 * 2. Se não há preview, tenta usar cache
 * 3. Se cache miss, constrói contexto via ThemeConfigResolver
 * 4. Cacheia resultado para próximas requisições
 *
 * PRECEDÊNCIA (via ThemeConfigResolver):
 * 1. DB (se is_active=1 e valor não null/empty)
 * 2. theme.json do tema selecionado
 * 3. Defaults
 *
 * PREVIEW:
 * - Usa session, não cache (isolamento por usuário)
 * - Força enabled=true para visualização
 * - Não persiste no banco
 */
final class ThemeContextFactory
{
    /**
     * Cria o ThemeContext baseado nas configurações atuais.
     *
     * @return ThemeContext Contexto imutável do tema
     */
    public static function make(): ThemeContext
    {
        // 1. Preview mode: bypass cache total (session-based, não vaza)
        if (self::isPreviewMode()) {
            return self::buildPreviewContext();
        }

        // 2. Normal mode: usa cache
        return self::buildCachedContext();
    }

    /**
     * Verifica se está em modo preview.
     */
    public static function isPreviewMode(): bool
    {
        return HandleThemePreview::hasActivePreview();
    }

    /**
     * Retorna o slug do preview ativo.
     */
    public static function getPreviewSlug(): ?string
    {
        return HandleThemePreview::getActivePreview();
    }

    /**
     * Constrói contexto para modo preview (sem cache).
     */
    private static function buildPreviewContext(): ThemeContext
    {
        try {
            $previewSlug = self::getPreviewSlug();
            $slug = ThemeConfigResolver::resolveSlug($previewSlug);
            $config = ThemeConfigResolver::resolveConfig($slug);
            $loginConfig = ThemeConfigResolver::resolveLoginConfig($slug);

            return new ThemeContext(
                enabled: true, // Preview sempre enabled para visualização
                slug: $slug,
                config: $config,
                loginConfig: $loginConfig,
                isPreview: true,
            );
        } catch (\Throwable $e) {
            Log::warning("[Theme] Preview build error", [
                "error" => $e->getMessage(),
                "preview_slug" => self::getPreviewSlug(),
            ]);

            // Fallback: retorna contexto normal (sem preview)
            return self::buildCachedContext();
        }
    }

    /**
     * Constrói contexto com cache.
     */
    private static function buildCachedContext(): ThemeContext
    {
        try {
            return ThemeCache::rememberContext(function () {
                return self::buildContext();
            });
        } catch (\Throwable $e) {
            Log::error("[Theme] Cache error, building without cache", [
                "error" => $e->getMessage(),
            ]);

            // Fallback: tenta sem cache
            return self::buildContextSafe();
        }
    }

    /**
     * Constrói o ThemeContext (lógica principal).
     *
     * @return ThemeContext Contexto construído
     */
    private static function buildContext(): ThemeContext
    {
        // 1. Verifica se tema está ativo
        $isActive = ThemeConfigResolver::isActive();

        if (!$isActive) {
            return ThemeContext::disabled();
        }

        // 2. Resolve slug (com validação de existência)
        $slug = ThemeConfigResolver::resolveSlug();

        // 3. Resolve configurações com precedência
        $config = ThemeConfigResolver::resolveConfig($slug);
        $loginConfig = ThemeConfigResolver::resolveLoginConfig($slug);

        return new ThemeContext(
            enabled: true,
            slug: $slug,
            config: $config,
            loginConfig: $loginConfig,
            isPreview: false,
        );
    }

    /**
     * Constrói contexto com tratamento de erro máximo.
     * Última linha de defesa para não derrubar o login.
     */
    private static function buildContextSafe(): ThemeContext
    {
        try {
            return self::buildContext();
        } catch (\Throwable $e) {
            Log::error("[Theme] Critical error building context", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            // Retorna contexto desabilitado para não quebrar a aplicação
            return ThemeContext::disabled();
        }
    }

    /**
     * Limpa todo o cache de tema.
     * Usar após aplicar tema, restaurar, rollback, etc.
     *
     * @deprecated Use ThemeCache::flush() diretamente
     */
    public static function clearCache(): void
    {
        ThemeCache::flush();

        // Também tenta limpar cache do ThemeHelper do package
        try {
            if (app()->bound("theme")) {
                app("theme")->clearCache();
            }
        } catch (\Throwable $e) {
            Log::debug("[Theme] Could not clear package cache", [
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Força rebuild do cache.
     * Útil após alterações que precisam refletir imediatamente.
     */
    public static function rebuild(): ThemeContext
    {
        ThemeCache::forgetContext();

        return self::buildCachedContext();
    }

    /**
     * Retorna a cache key principal.
     *
     * @deprecated Use ThemeCache::KEY_CONTEXT diretamente
     */
    public static function getCacheKey(): string
    {
        return ThemeCache::KEY_CONTEXT;
    }
}
