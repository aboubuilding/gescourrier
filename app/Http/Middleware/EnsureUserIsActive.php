<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->etat !== 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Votre compte est suspendu. Veuillez contacter l\'administration.',
                    'code' => 'ACCOUNT_SUSPENDED'
                ], 403)
                : redirect()->route('login')
                    ->withErrors(['email' => 'Votre compte est suspendu.'])
                    ->withInput($request->only('email'));
        }

        return $next($request);
    }
}