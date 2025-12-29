<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para gerenciar preview de tema por sessão (admin-only).
 *
 * FUNCIONALIDADE:
 * - Captura ?theme_preview={slug} na query string
 * - Armazena em session('theme_preview') se válido
 * - Captura ?clear_preview=1 para limpar a sessão
 * - Só funciona para usuários autenticados com permissão 'settings'
 *
 * IMPORTANTE:
 * - Não persiste no banco - apenas na sessão do usuário
 * - Não afeta outros usuários
 * - ThemeContextFactory respeita session('theme_preview')
 */
class HandleThemePreview
{
    /**
     * Pasta base onde ficam os temas (disk public).
     */
    private const THEMES_BASE_PATH = 'themes';

    /**
     * Session key para armazenar o preview.
     */
    public const SESSION_KEY = 'theme_preview';

    /**
     * Query param para ativar preview.
     */
    private const QUERY_PARAM_PREVIEW = 'theme_preview';

    /**
     * Query param para limpar preview.
     */
    private const QUERY_PARAM_CLEAR = 'clear_preview';

    /**
     * Prefixo das rotas admin.
     */
    private string $adminPrefix;

    public function __construct()
    {
        $this->adminPrefix = config('app.admin_path', 'admin');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // EARLY RETURN: Só processa rotas admin
        if (! $this->isAdminRoute($request)) {
            return $next($request);
        }

        // Processa query params de preview (apenas se autenticado com permissão)
        if ($this->canManageTheme()) {
            $this->processPreviewParams($request);
        }

        return $next($request);
    }

    /**
     * Verifica se a request é para uma rota admin.
     */
    private function isAdminRoute(Request $request): bool
    {
        $path = $request->path();

        return $path === $this->adminPrefix ||
            str_starts_with($path, $this->adminPrefix.'/');
    }

    /**
     * Verifica se o usuário pode gerenciar temas.
     */
    private function canManageTheme(): bool
    {
        try {
            $guard = auth()->guard('user');

            if (! $guard->check()) {
                return false;
            }

            $user = $guard->user();

            // Status ativo
            if (! isset($user->status) || (int) $user->status !== 1) {
                return false;
            }

            // Permissão settings
            if (! function_exists('bouncer') || ! bouncer()->hasPermission('settings')) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Processa os query params de preview.
     */
    private function processPreviewParams(Request $request): void
    {
        // Limpar preview
        if ($request->has(self::QUERY_PARAM_CLEAR)) {
            $this->clearPreview();

            return;
        }

        // Ativar preview
        if ($request->has(self::QUERY_PARAM_PREVIEW)) {
            $slug = $request->query(self::QUERY_PARAM_PREVIEW);
            $this->setPreview($slug, $request);
        }
    }

    /**
     * Define o tema de preview na sessão.
     */
    private function setPreview(string $slug, Request $request): void
    {
        $sanitized = $this->sanitizeSlug($slug);

        if ($sanitized === '') {
            return;
        }

        // Valida existência do tema
        if (! $this->themeExists($sanitized)) {
            Log::warning('[Theme] Preview requested for non-existent theme', [
                'slug'    => $sanitized,
                'user_id' => auth()->guard('user')->id(),
                'ip'      => $request->ip(),
            ]);

            return;
        }

        session([self::SESSION_KEY => $sanitized]);

        Log::info('[Theme] Preview activated', [
            'slug'    => $sanitized,
            'user_id' => auth()->guard('user')->id(),
        ]);
    }

    /**
     * Limpa o preview da sessão.
     */
    private function clearPreview(): void
    {
        $previous = session(self::SESSION_KEY);

        session()->forget(self::SESSION_KEY);

        if ($previous) {
            Log::info('[Theme] Preview cleared', [
                'previous_slug' => $previous,
                'user_id'       => auth()->guard('user')->id(),
            ]);
        }
    }

    /**
     * Sanitiza o slug do tema.
     */
    private function sanitizeSlug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-_]/', '', $value) ?? '';
        $value = trim($value, '-_');

        return substr($value, 0, 50);
    }

    /**
     * Verifica se o tema existe no storage.
     */
    private function themeExists(string $slug): bool
    {
        if ($slug === 'default') {
            return true;
        }

        $base = trim(self::THEMES_BASE_PATH, '/');

        return Storage::disk('public')->exists("{$base}/{$slug}/theme.json");
    }

    /**
     * Retorna o slug do preview ativo (helper estático).
     */
    public static function getActivePreview(): ?string
    {
        return session(self::SESSION_KEY);
    }

    /**
     * Verifica se há preview ativo (helper estático).
     */
    public static function hasActivePreview(): bool
    {
        return session()->has(self::SESSION_KEY);
    }
}
