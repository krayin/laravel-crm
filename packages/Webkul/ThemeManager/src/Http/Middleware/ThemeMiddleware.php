<?php

namespace Webkul\ThemeManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Verificar se o tema está ativo
        if (! app('theme')->isActive()) {
            return $response;
        }

        // Verificar se é uma resposta HTML
        if (! $response instanceof Response) {
            return $response;
        }

        $content = $response->getContent();

        // Verificar se há tag </head> no conteúdo
        if (stripos($content, '</head>') === false) {
            return $response;
        }

        // Renderizar o componente de estilos do tema
        $themeStyles = view('theme-manager::components.theme-styles')->render();

        // Injetar o CSS antes do </head>
        $content = str_ireplace('</head>', $themeStyles.'</head>', $content);

        $response->setContent($content);

        return $response;
    }
}
