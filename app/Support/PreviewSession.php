<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

/**
 * Gerencia estado de preview de tema na session.
 *
 * IMPORTANTE: Preview NUNCA toca no cache global.
 * Tudo fica isolado na session do usuário.
 *
 * Estrutura na session:
 * - brandkit.preview.enabled = bool
 * - brandkit.preview.scope_key = string
 * - brandkit.preview.theme_slug = string
 * - brandkit.preview.overrides = array (flat keys => values)
 * - brandkit.preview.css_admin = string
 * - brandkit.preview.css_login = string
 * - brandkit.preview.started_at = timestamp
 */
final class PreviewSession
{
    private const PREFIX = 'brandkit.preview';

    private const KEY_ENABLED = self::PREFIX . '.enabled';
    private const KEY_SCOPE = self::PREFIX . '.scope_key';
    private const KEY_THEME = self::PREFIX . '.theme_slug';
    private const KEY_OVERRIDES = self::PREFIX . '.overrides';
    private const KEY_CSS_ADMIN = self::PREFIX . '.css_admin';
    private const KEY_CSS_LOGIN = self::PREFIX . '.css_login';
    private const KEY_STARTED_AT = self::PREFIX . '.started_at';

    /**
     * Verifica se preview está ativo.
     */
    public static function isActive(): bool
    {
        return Session::get(self::KEY_ENABLED, false) === true;
    }

    /**
     * Obtém o scope do preview atual.
     */
    public static function getScopeKey(): string
    {
        return Session::get(self::KEY_SCOPE, 'global');
    }

    /**
     * Obtém o tema do preview atual.
     */
    public static function getThemeSlug(): string
    {
        return Session::get(self::KEY_THEME, 'default');
    }

    /**
     * Obtém overrides do preview (flat keys).
     *
     * @return array<string, mixed>
     */
    public static function getOverrides(): array
    {
        return Session::get(self::KEY_OVERRIDES, []);
    }

    /**
     * Obtém CSS admin do preview.
     */
    public static function getCssAdmin(): string
    {
        return Session::get(self::KEY_CSS_ADMIN, '');
    }

    /**
     * Obtém CSS login do preview.
     */
    public static function getCssLogin(): string
    {
        return Session::get(self::KEY_CSS_LOGIN, '');
    }

    /**
     * Obtém timestamp de início do preview.
     */
    public static function getStartedAt(): ?int
    {
        return Session::get(self::KEY_STARTED_AT);
    }

    /**
     * Inicia um preview completo.
     *
     * @param string $scopeKey
     * @param string $themeSlug
     * @param array<string, mixed> $overrides
     * @param string $cssAdmin
     * @param string $cssLogin
     */
    public static function start(
        string $scopeKey,
        string $themeSlug,
        array $overrides = [],
        string $cssAdmin = '',
        string $cssLogin = '',
    ): void {
        Session::put([
            self::KEY_ENABLED => true,
            self::KEY_SCOPE => $scopeKey,
            self::KEY_THEME => $themeSlug,
            self::KEY_OVERRIDES => $overrides,
            self::KEY_CSS_ADMIN => $cssAdmin,
            self::KEY_CSS_LOGIN => $cssLogin,
            self::KEY_STARTED_AT => time(),
        ]);
    }

    /**
     * Inicia preview apenas de tema (sem overrides customizados).
     */
    public static function startThemePreview(string $themeSlug, string $scopeKey = 'global'): void
    {
        self::start($scopeKey, $themeSlug);
    }

    /**
     * Atualiza overrides do preview atual.
     *
     * @param array<string, mixed> $overrides
     */
    public static function updateOverrides(array $overrides): void
    {
        if (!self::isActive()) {
            return;
        }

        $current = self::getOverrides();
        $merged = array_merge($current, $overrides);

        Session::put(self::KEY_OVERRIDES, $merged);
    }

    /**
     * Remove um override específico do preview.
     */
    public static function removeOverride(string $key): void
    {
        if (!self::isActive()) {
            return;
        }

        $overrides = self::getOverrides();
        unset($overrides[$key]);

        Session::put(self::KEY_OVERRIDES, $overrides);
    }

    /**
     * Atualiza CSS do preview.
     */
    public static function updateCss(string $cssAdmin, string $cssLogin): void
    {
        if (!self::isActive()) {
            return;
        }

        Session::put(self::KEY_CSS_ADMIN, $cssAdmin);
        Session::put(self::KEY_CSS_LOGIN, $cssLogin);
    }

    /**
     * Encerra o preview e limpa a session.
     */
    public static function clear(): void
    {
        Session::forget([
            self::KEY_ENABLED,
            self::KEY_SCOPE,
            self::KEY_THEME,
            self::KEY_OVERRIDES,
            self::KEY_CSS_ADMIN,
            self::KEY_CSS_LOGIN,
            self::KEY_STARTED_AT,
        ]);
    }

    /**
     * Retorna todos os dados do preview atual.
     *
     * @return array{
     *     enabled: bool,
     *     scope_key: string,
     *     theme_slug: string,
     *     overrides: array,
     *     css_admin: string,
     *     css_login: string,
     *     started_at: int|null
     * }
     */
    public static function all(): array
    {
        return [
            'enabled' => self::isActive(),
            'scope_key' => self::getScopeKey(),
            'theme_slug' => self::getThemeSlug(),
            'overrides' => self::getOverrides(),
            'css_admin' => self::getCssAdmin(),
            'css_login' => self::getCssLogin(),
            'started_at' => self::getStartedAt(),
        ];
    }

    /**
     * Verifica se o preview está expirado (mais de 30 minutos).
     */
    public static function isExpired(int $maxMinutes = 30): bool
    {
        $startedAt = self::getStartedAt();

        if ($startedAt === null) {
            return false;
        }

        $elapsed = time() - $startedAt;
        $maxSeconds = $maxMinutes * 60;

        return $elapsed > $maxSeconds;
    }

    /**
     * Limpa preview se estiver expirado.
     */
    public static function clearIfExpired(int $maxMinutes = 30): bool
    {
        if (self::isActive() && self::isExpired($maxMinutes)) {
            self::clear();
            return true;
        }

        return false;
    }
}
