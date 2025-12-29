<?php

namespace App\Http\Middleware;

use App\Support\ThemeContextFactory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShareThemeContext
{
    public function __construct(
        private readonly ThemeContextFactory $factory,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        // So injeta no Admin (inclui login do admin) — evita custo desnecessario.
        if (! $this->isAdminRoute($request)) {
            return $next($request);
        }

        $themeContext = $this->factory->create($request);

        View::share('themeContext', $themeContext);
        $request->attributes->set('themeContext', $themeContext);

        return $next($request);
    }

    private function isAdminRoute(Request $request): bool
    {
        $adminPath = config('app.admin_path', 'admin');

        // cobre /admin e /admin/*
        return $request->is($adminPath) || $request->is("{$adminPath}/*");
    }
}
