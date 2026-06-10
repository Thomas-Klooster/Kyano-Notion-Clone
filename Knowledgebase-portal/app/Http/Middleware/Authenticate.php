<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     * 
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (!$request->headers->has('Authorization') && $request->cookie('accessToken')) {
            $request->headers->set('Authorization', 'Bearer '.$request->cookie('accessToken'));
        }

        if (!Auth::guard('sanctum')->check()) {
            throw new AuthenticationException('Unauthenticated.');
        }

        Auth::shouldUse('sanctum');
        $request->setUserResolver(fn() => Auth::guard('sanctum')->user());
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken && !$token->can('access')) {
            throw new AuthenticationException('Unauthenticated.');
        }
        return $next($request);
    }
}