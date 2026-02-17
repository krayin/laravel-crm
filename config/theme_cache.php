<?php

/**
 * Configuração centralizada de cache para Theme/BrandKit.
 *
 * Este arquivo é a fonte única de verdade para:
 * - Versões de cache keys
 * - TTLs (curto e longo)
 * - Registro de todas as keys utilizadas
 *
 * IMPORTANTE:
 * - Após editar este arquivo, rodar: php artisan config:clear
 * - Para invalidar cache existente: php artisan theme:cache:clean --reason=config-change
 *
 * QUANDO FAZER VERSION BUMP:
 * - Mudança estrutural no schema de cache (novo campo, tipo diferente)
 * - Alteração na lógica de merge/resolução de tema
 * - Migração de dados que afeta cache
 * - Ao atualizar pacote ThemeManager para versão major
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Versão Global do Cache
    |--------------------------------------------------------------------------
    |
    | Sufixo adicionado às keys versionadas. Incrementar quando houver
    | mudança estrutural que invalide cache antigo.
    |
    */
    'version' => env('THEME_CACHE_VERSION', 'v2'),

    /*
    |--------------------------------------------------------------------------
    | TTLs (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Tempos de expiração em segundos.
    | - short: Para dados que mudam frequentemente (lista de temas, seleção)
    | - long: Para dados estáveis (configuração, contexto)
    |
    */
    'ttl' => [
        'short' => (int) env('THEME_CACHE_TTL_SHORT', 300),   // 5 minutos
        'long'  => (int) env('THEME_CACHE_TTL_LONG', 3600),   // 1 hora
    ],

    /*
    |--------------------------------------------------------------------------
    | Registry de Cache Keys
    |--------------------------------------------------------------------------
    |
    | Todas as cache keys utilizadas pelo sistema de temas.
    | Formato: 'base_name' => [
    |     'version' => 'v1' (opcional, se não tiver usa key exata)
    |     'ttl' => 'short' ou 'long'
    |     'note' => descrição para documentação
    | ]
    |
    */
    'keys' => [
        // ThemeHelper (pacote) - sem versão, key exata
        'theme_config' => [
            'ttl'  => 'long',
            'note' => 'ThemeHelper::getConfig() - config do pacote',
        ],

        // ThemeSelectionResolver (app)
        'theme.selected_slug' => [
            'version' => 'v1',
            'ttl'     => 'short',
            'note'    => 'ThemeSelectionResolver::getSelectedThemeSlug()',
        ],

        // ThemeCache (app)
        'theme.context' => [
            'version' => 'v2',
            'ttl'     => 'long',
            'note'    => 'ThemeCache::getContext() - contexto completo',
        ],

        'theme.config' => [
            'version' => 'v2',
            'ttl'     => 'long',
            'note'    => 'ThemeCache::getConfig() - config do DB',
        ],

        'theme.available' => [
            'version' => 'v2',
            'ttl'     => 'short',
            'note'    => 'ThemeCache::getAvailable() - lista de temas',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Keys (para limpeza durante migração)
    |--------------------------------------------------------------------------
    |
    | Keys de versões anteriores que devem ser limpas no flush.
    | Manter aqui por pelo menos 1 ciclo de deploy.
    |
    */
    'legacy_keys' => [
        'theme_context.factory.v1',
        'theme.available.v1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Patterns (para documentação)
    |--------------------------------------------------------------------------
    |
    | Patterns de keys dinâmicas. Não são usados para Cache::forget
    | (não é possível), mas servem para documentação e troubleshooting.
    |
    */
    'patterns' => [
        'brandkit_resolved' => 'brand_kit.resolved.v{version}.{scope}.{slug}',
    ],

];
