<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar permissão de gerenciamento de tema.
 *
 * Protege as rotas de Settings > Theme usando o sistema ACL do Krayin (Bouncer).
 * Upgrade-safe: não modifica packages/, apenas adiciona camada de proteção.
 *
 * Permissões verificadas:
 * - settings.theme.view: Visualizar página de configurações
 * - settings.theme.edit: Aplicar/trocar temas, preview
 * - settings.theme.restore: Restaurar padrão, rollback
 */
class ThemePermission
{
    /**
     * Mapeamento de rotas para permissões.
     */
    private const ROUTE_PERMISSIONS = [
        'admin.settings.theme.index'    => 'settings.theme.view',
        'admin.settings.theme.update'   => 'settings.theme.edit',
        'admin.settings.theme.restore'  => 'settings.theme.restore',
        'admin.settings.theme.rollback' => 'settings.theme.restore',
    ];

    /**
     * Permissão padrão para rotas não mapeadas.
     */
    private const DEFAULT_PERMISSION = 'settings.theme.view';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se é uma rota de tema
        $routeName = $request->route()?->getName();

        if (! $this->isThemeRoute($routeName)) {
            return $next($request);
        }

        // Verifica autenticação
        if (! $this->isAuthenticated()) {
            return $this->denyAccess($request, 'not_authenticated');
        }

        // Verifica permissão específica da rota
        $permission = $this->getRequiredPermission($routeName);

        if (! $this->hasPermission($permission)) {
            return $this->denyAccess($request, 'no_permission', [
                'required' => $permission,
                'route'    => $routeName,
            ]);
        }

        return $next($request);
    }

    /**
     * Verifica se é uma rota de gerenciamento de tema.
     */
    private function isThemeRoute(?string $routeName): bool
    {
        if ($routeName === null) {
            return false;
        }

        return str_starts_with($routeName, 'admin.settings.theme');
    }

    /**
     * Verifica se o usuário está autenticado no guard 'user'.
     */
    private function isAuthenticated(): bool
    {
        try {
            $guard = auth()->guard('user');

            if (! $guard->check()) {
                return false;
            }

            $user = $guard->user();

            // Verifica se usuário está ativo
            return isset($user->status) && (int) $user->status === 1;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Obtém a permissão necessária para a rota.
     */
    private function getRequiredPermission(?string $routeName): string
    {
        if ($routeName === null) {
            return self::DEFAULT_PERMISSION;
        }

        return self::ROUTE_PERMISSIONS[$routeName] ?? self::DEFAULT_PERMISSION;
    }

    /**
     * Verifica se o usuário tem a permissão.
     *
     * IMPORTANTE: Se o role tem permission_type='all', tem todas as permissões.
     * Caso contrário, verifica permissão específica via bouncer.
     */
    private function hasPermission(string $permission): bool
    {
        try {
            $user = auth()->guard('user')->user();

            // Se role tem permission_type='all', libera tudo
            if ($user->role && $user->role->permission_type === 'all') {
                return true;
            }

            // Verifica permissão específica via bouncer
            if (! function_exists('bouncer')) {
                // Se bouncer não existe, fallback para role check
                return $user->role && $user->role->permission_type === 'all';
            }

            return bouncer()->hasPermission($permission);
        } catch (\Throwable $e) {
            Log::warning('[Theme] Permission check error', [
                'permission' => $permission,
                'error'      => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Nega acesso e loga a tentativa.
     */
    private function denyAccess(Request $request, string $reason, array $extra = []): Response
    {
        Log::warning('[Theme] Access denied to theme settings', array_merge([
            'reason'  => $reason,
            'route'   => $request->route()?->getName(),
            'ip'      => $request->ip(),
            'user_id' => auth()->guard('user')->id(),
        ], $extra));

        // Retorna 403 com mensagem amigável
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Você não tem permissão para acessar esta página.',
            ], 403);
        }

        abort(403, 'Você não tem permissão para gerenciar temas.');
    }
}
