<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Centralizador de cache para o sistema de temas.
 *
 * Responsabilidades:
 * - Definir todas as cache keys em um único lugar
 * - Prover métodos de invalidação granular e total
 * - Garantir que preview por sessão não vaze para cache global
 *
 * IMPORTANTE: Preview usa session, não cache. Isso garante isolamento por usuário.
 */
final class ThemeCache
{
    /**
     * Cache keys centralizadas.
     */
    public const KEY_CONTEXT = 'theme.context.v2';

    public const KEY_CONFIG = 'theme.config.v2';

    public const KEY_AVAILABLE = 'theme.available.v2';

    /**
     * Keys legadas (para limpeza durante migração).
     */
    private const LEGACY_KEYS = [
        'theme_context.factory.v1',
        'theme_config',
        'theme.available.v1',
    ];

    /**
     * TTL padrão em segundos (1 hora).
     */
    public const DEFAULT_TTL = 3600;

    /**
     * TTL para lista de temas disponíveis (5 minutos).
     */
    public const AVAILABLE_TTL = 300;

    /**
     * Obtém valor do cache de contexto.
     */
    public static function getContext(): ?ThemeContext
    {
        return Cache::get(self::KEY_CONTEXT);
    }

    /**
     * Armazena contexto no cache.
     */
    public static function putContext(ThemeContext $context, ?int $ttl = null): void
    {
        Cache::put(self::KEY_CONTEXT, $context, $ttl ?? self::DEFAULT_TTL);
    }

    /**
     * Obtém configuração do banco (cacheada).
     */
    public static function getConfig(): ?object
    {
        return Cache::get(self::KEY_CONFIG);
    }

    /**
     * Armazena configuração no cache.
     */
    public static function putConfig(object $config, ?int $ttl = null): void
    {
        Cache::put(self::KEY_CONFIG, $config, $ttl ?? self::DEFAULT_TTL);
    }

    /**
     * Obtém lista de temas disponíveis (cacheada).
     */
    public static function getAvailable(): ?array
    {
        return Cache::get(self::KEY_AVAILABLE);
    }

    /**
     * Armazena lista de temas disponíveis.
     */
    public static function putAvailable(array $themes, ?int $ttl = null): void
    {
        Cache::put(self::KEY_AVAILABLE, $themes, $ttl ?? self::AVAILABLE_TTL);
    }

    /**
     * Invalida cache do contexto.
     */
    public static function forgetContext(): void
    {
        Cache::forget(self::KEY_CONTEXT);
    }

    /**
     * Invalida cache da configuração.
     */
    public static function forgetConfig(): void
    {
        Cache::forget(self::KEY_CONFIG);
    }

    /**
     * Invalida cache de temas disponíveis.
     */
    public static function forgetAvailable(): void
    {
        Cache::forget(self::KEY_AVAILABLE);
    }

    /**
     * Invalida TODO o cache de tema.
     * Usar após aplicar tema, restaurar, rollback, etc.
     */
    public static function flush(): void
    {
        self::forgetContext();
        self::forgetConfig();
        self::forgetAvailable();

        // Limpa keys legadas também
        self::flushLegacy();

        Log::debug('[ThemeCache] All theme caches flushed');
    }

    /**
     * Limpa keys legadas (migração).
     */
    private static function flushLegacy(): void
    {
        foreach (self::LEGACY_KEYS as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Verifica se há cache de contexto válido.
     */
    public static function hasContext(): bool
    {
        return Cache::has(self::KEY_CONTEXT);
    }

    /**
     * Verifica se há cache de config válido.
     */
    public static function hasConfig(): bool
    {
        return Cache::has(self::KEY_CONFIG);
    }

    /**
     * Executa callback com cache de contexto.
     * Se cache existe, retorna. Senão, executa callback e cacheia.
     *
     * @param  callable  $resolver  Função que retorna ThemeContext
     * @param  int|null  $ttl  TTL em segundos
     */
    public static function rememberContext(callable $resolver, ?int $ttl = null): ThemeContext
    {
        $cached = self::getContext();

        if ($cached !== null) {
            return $cached;
        }

        $context = $resolver();

        self::putContext($context, $ttl);

        return $context;
    }

    /**
     * Executa callback com cache de config.
     *
     * @param  callable  $resolver  Função que retorna object
     * @param  int|null  $ttl  TTL em segundos
     */
    public static function rememberConfig(callable $resolver, ?int $ttl = null): object
    {
        $cached = self::getConfig();

        if ($cached !== null) {
            return $cached;
        }

        $config = $resolver();

        self::putConfig($config, $ttl);

        return $config;
    }
}
