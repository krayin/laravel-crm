<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class Locale
{
    public function __construct(
        Application $app,
        Request $request
    )
    {
        $this->app = $app;

        $this->request = $request;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = request()->get('admin_locale');

        if ($locale) {
            app()->setLocale($locale);

            session()->put('admin_locale', $locale);
        } else {
            if ($locale = session()->get('admin_locale')) {
                app()->setLocale($locale);
            } else {
                app()->setLocale(app()->getLocale());
            }
        }

        unset($request['admin_locale']);

        return $next($request);
    }
}