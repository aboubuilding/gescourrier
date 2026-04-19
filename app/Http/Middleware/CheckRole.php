<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles  Rôles autorisés (ex: 'admin', 'chef_service')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Non authentifié.', 'code' => 'UNAUTHENTICATED'], 401)
                : redirect()->route('login');
        }

        $userRole = Auth::user()->role;
        
        if (!in_array($userRole, $roles)) {
            \Log::warning("Accès refusé", [
                'user_id' => Auth::id(),
                'user_role' => $userRole,
                'required_roles' => $roles,
                'path' => $request->path()
            ]);

            return $request->expectsJson()
                ? response()->json([
                    'success' => false, 
                    'message' => 'Accès refusé. Rôle insuffisant.',
                    'code' => 'FORBIDDEN'
                ], 403)
                : abort(403, 'Vous n\'avez pas les permissions requises.');
        }

        return $next($request);
    }
}