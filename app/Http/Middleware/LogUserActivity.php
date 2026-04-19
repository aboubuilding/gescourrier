<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne logger que les méthodes qui modifient des données
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            Log::info('Action utilisateur', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role ?? 'guest',
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                // Attention : ne pas logger les mots de passe ou données sensibles !
                // 'payload' => $request->except(['password', 'password_confirmation']),
            ]);
        }

        return $next($request);
    }
}