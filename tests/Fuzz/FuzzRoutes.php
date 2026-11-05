<?php

namespace Tests\Fuzz;

class FuzzRoutes
{
    /**
     * Routes with effects outside the database transaction (session, installer, OAuth, external APIs).
     */
    public const EXCLUDED = '#(^admin/logout|^admin/login|^admin/forget-password|^admin/reset-password|^install|/google/|google-export|inbound-parse|create-by-ai|magic-ai|^web-forms/)#';

    protected static ?array $routes = null;

    /**
     * Every registered route, read from `route:list` so the dataset can be built before the
     * application boots.
     */
    public static function all(): array
    {
        if (self::$routes !== null) {
            return self::$routes;
        }

        $artisan = escapeshellarg(dirname(__DIR__, 2).'/artisan');

        $output = shell_exec(escapeshellarg(PHP_BINARY).' '.$artisan.' route:list --json 2>/dev/null');

        $routes = json_decode((string) $output, true) ?: [];

        return self::$routes = array_map(fn (array $route) => [
            'uri' => $route['uri'],
            'methods' => explode('|', $route['method']),
            'name' => $route['name'] ?? $route['uri'],
        ], $routes);
    }

    /**
     * Admin routes answering one of the methods, keyed by "METHOD uri" for readable test names.
     */
    public static function admin(array $methods, ?bool $withParameters = null): array
    {
        $dataset = [];

        foreach (self::all() as $route) {
            if (
                ! str_starts_with($route['uri'], 'admin')
                || preg_match(self::EXCLUDED, $route['uri'])
                || ($withParameters !== null && $withParameters !== self::hasParameters($route['uri']))
            ) {
                continue;
            }

            foreach (array_intersect($methods, $route['methods']) as $method) {
                $dataset["{$method} {$route['uri']}"] = [$method, $route['uri']];
            }
        }

        return $dataset;
    }

    public static function hasParameters(string $uri): bool
    {
        return (bool) preg_match('/\{\w+\??\}/', $uri);
    }

    /**
     * Fill every route parameter with the given value.
     */
    public static function fill(string $uri, string $value): string
    {
        return '/'.preg_replace('/\{\w+\??\}/', rawurlencode($value), $uri);
    }
}
