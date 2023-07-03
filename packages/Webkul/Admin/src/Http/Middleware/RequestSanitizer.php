<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestSanitizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedMethods = ['POST', 'PUT', 'PATCH'];

        if (in_array
            (
                $request->getMethod(),
                $allowedMethods
            )
        ) {
            $input = $request->all();

            array_walk_recursive($input, function (&$input) {
                $input = strip_tags($input);
            });

            $request->merge($input);
        }

        return $next($request);
    }
}
