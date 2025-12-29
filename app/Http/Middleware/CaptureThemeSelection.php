<?php

namespace App\Http\Middleware;

use App\Support\ThemeCache;
use App\Support\ThemeConfigResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware blindado para captura de selected_theme no Theme Manager.
 *
 * SEGURANÇA:
 * - Só roda na rota exata admin.settings.theme.update
 * - Exige guard 'user' autenticado, ativo e com permissão 'settings'
 * - Sanitiza e valida slug por existência real do tema
 * - Salva via Query Builder (sem $fillable)
 * - Limpa apenas cache específico (sem cache:clear geral)
 * - Loga tentativas negadas e mudanças efetivas
 */
class CaptureThemeSelection
{
    /**
     * Nome exato da rota do Theme Manager (do package).
     */
    private const ROUTE_NAME = 'admin.settings.theme.update';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Deixa o controller do package rodar normalmente
        $response = $next($request);

        // Captura depois do controller (post-action)
        if (! $this->shouldCapture($request)) {
            return $response;
        }

        $this->saveThemeSelection($request);

        return $response;
    }

    /**
     * Verifica TODAS as condições de segurança.
     */
    private function shouldCapture(Request $request): bool
    {
        // 1) Rota exata
        $routeName = $request->route()?->getName();
        if ($routeName !== self::ROUTE_NAME) {
            return false;
        }

        // 2) Método permitido
        if (
            ! in_array(
                strtoupper($request->method()),
                ['POST', 'PUT', 'PATCH'],
                true,
            )
        ) {
            return false;
        }

        // 3) Auth + status + permissão (guard user)
        try {
            $guard = auth()->guard('user');

            if (! $guard->check()) {
                $this->logDenied($request, 'not_authenticated');

                return false;
            }

            $user = $guard->user();

            // status === 1 (usuário ativo)
            if (! isset($user->status) || (int) $user->status !== 1) {
                $this->logDenied($request, 'inactive_user', [
                    'user_id' => $user->id ?? null,
                ]);

                return false;
            }

            // Bouncer permission (settings)
            if (
                ! function_exists('bouncer') ||
                ! bouncer()->hasPermission('settings')
            ) {
                $this->logDenied($request, 'no_permission', [
                    'user_id' => $user->id ?? null,
                ]);

                return false;
            }
        } catch (\Throwable $e) {
            // Falha inesperada em auth/bouncer => negar por segurança
            Log::warning(
                '[Theme] CaptureThemeSelection denied due to auth/bouncer error',
                [
                    'error' => $e->getMessage(),
                    'ip'    => $request->ip(),
                ],
            );

            return false;
        }

        // 4) Campo existe
        if (! $request->has('selected_theme')) {
            return false;
        }

        return true;
    }

    /**
     * Salva o tema selecionado no banco.
     */
    private function saveThemeSelection(Request $request): void
    {
        $raw = (string) $request->input('selected_theme', '');

        // DEBUG: Log what's being received
        Log::debug('[Theme] CaptureThemeSelection received', [
            'raw_selected_theme' => $raw,
            'all_input'          => $request->only(['selected_theme', 'is_active']),
            'session_preview'    => session('theme_preview'),
        ]);

        $slug = ThemeConfigResolver::sanitizeSlug($raw);

        // Validar existência real do tema. Se inválido => fallback.
        if (! ThemeConfigResolver::themeExists($slug)) {
            Log::warning(
                '[Theme] Invalid selected_theme slug. Falling back to default.',
                [
                    'selected_theme' => $slug,
                    'fallback'       => ThemeConfigResolver::DEFAULT_SLUG,
                    'ip'             => $request->ip(),
                    'user_id'        => auth()->guard('user')->id(),
                ],
            );
            $slug = ThemeConfigResolver::DEFAULT_SLUG;
        }

        // Busca o tema atual para salvar como previous_theme
        $currentTheme =
            DB::table('theme_configs')
                ->where('id', 1)
                ->value('selected_theme') ?? ThemeConfigResolver::DEFAULT_SLUG;

        // Só atualiza previous_theme se realmente mudou
        $updateData = [
            'selected_theme' => $slug,
            'updated_at'     => now(),
        ];

        if ($currentTheme !== $slug) {
            $updateData['previous_theme'] = $currentTheme;
        }

        // Persistir sem depender de Model/$fillable
        DB::table('theme_configs')->where('id', 1)->update($updateData);

        // Limpa TODO o cache de tema via helper centralizado
        ThemeCache::flush();

        // Limpa a sessão de preview (tema foi aplicado, não precisa mais do preview)
        session()->forget(HandleThemePreview::SESSION_KEY);

        // AUDITORIA: Log estruturado da mudança de tema
        Log::info('theme.changed', [
            'user_id'    => auth()->guard('user')->id(),
            'user_email' => auth()->guard('user')->user()?->email,
            'old'        => $currentTheme,
            'new'        => $slug,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp'  => now()->toIso8601String(),
        ]);
    }

    /**
     * Loga tentativas negadas.
     */
    private function logDenied(
        Request $request,
        string $reason,
        array $extra = [],
    ): void {
        Log::warning(
            '[Theme] CaptureThemeSelection denied',
            array_merge(
                [
                    'reason'  => $reason,
                    'route'   => $request->route()?->getName(),
                    'method'  => $request->method(),
                    'ip'      => $request->ip(),
                    'user_id' => auth()->guard('user')->id() ?? null,
                ],
                $extra,
            ),
        );
    }
}
