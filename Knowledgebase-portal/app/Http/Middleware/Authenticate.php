<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next): Response
{
    if (Auth::guard('sanctum')->check()) {
        Auth::shouldUse('sanctum');
        $request->setUserResolver(fn() => Auth::guard('sanctum')->user());
        return $next($request);
    }

    throw new AuthenticationException('Unauthenticated.');
}
}
