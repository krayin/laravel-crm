<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user() ?? Auth::guard('sanctum')->user();
        
        if (!$user) {
            return redirect()->route('krayin.home'); 
        }

        if (!$user->is_super && $user->is_super !== 1 && $user->is_super !== '1') {
           
            return redirect()->route('krayin.home');
        }

        return $next($request);
    }
}