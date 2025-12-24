<?php

namespace App\Http\Middleware;

use App\Support\ThemeContext;
use App\Support\ThemeContextFactory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que compartilha o ThemeContext com views do admin.
 *
 * OTIMIZAÇÃO: Só processa para rotas /admin/* para evitar overhead
 * em outras rotas web (API, webhooks, etc).
 */
class ShareThemeContext
{
    /**
     * Prefixo das rotas admin (lido do config).
     */
    private string $adminPrefix;

    public function __construct()
    {
        $this->adminPrefix = config("app.admin_path", "admin");
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // EARLY RETURN: Só processa rotas admin
        if (!$this->isAdminRoute($request)) {
            // Ainda compartilha contexto disabled para evitar erros em views
            View::share("themeContext", ThemeContext::disabled());
            return $next($request);
        }

        // Cria o contexto do tema (com fallback seguro)
        $themeContext = ThemeContextFactory::make();

        // Compartilha com todas as views
        View::share("themeContext", $themeContext);

        // Também disponibiliza via request para uso em controllers se necessário
        $request->attributes->set("themeContext", $themeContext);

        return $next($request);
    }

    /**
     * Verifica se a request é para uma rota admin.
     */
    private function isAdminRoute(Request $request): bool
    {
        $path = $request->path();

        // Rota raiz do admin ou sub-rotas
        return $path === $this->adminPrefix ||
            str_starts_with($path, $this->adminPrefix . "/");
    }
}
