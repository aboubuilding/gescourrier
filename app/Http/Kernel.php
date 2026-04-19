<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
            // 🔐 Middleware personnalisé : vérifie que l'utilisateur a etat=1
            \App\Http\Middleware\EnsureUserIsActive::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
            // 📡 Middleware optionnel : force le format JSON pour les réponses API
            // \App\Http\Middleware\ForceJsonResponse::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // ── Middlewares Laravel par défaut ──
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // ── 🔐 Middlewares personnalisés du projet ──
        
        /**
         * Vérifie que l'utilisateur connecté a etat=1 (actif).
         * Si etat=2 (suspendu), déconnecte et retourne une erreur.
         * 
         * Utilisation : Route::middleware(['auth', 'check.active'])->group(...)
         */
        'check.active' => \App\Http\Middleware\EnsureUserIsActive::class,
        
        /**
         * Contrôle d'accès par rôle.
         * Accepte plusieurs rôles en paramètre : 'role:admin,chef_service'
         * 
         * Utilisation : Route::middleware('role:admin,super_admin')->group(...)
         */
        'role' => \App\Http\Middleware\CheckRole::class,
        
        /**
         * Journalise les actions critiques (POST/PUT/DELETE) pour l'audit.
         * 
         * Utilisation : Route::middleware('log.activity')->post(...)
         */
        'log.activity' => \App\Http\Middleware\LogUserActivity::class,
        
        /**
         * Force le format JSON pour les réponses, même si le client demande du HTML.
         * Utile pour les routes API consommées par JS/mobile.
         * 
         * Utilisation : Route::middleware('force.json')->get(...)
         */
        'force.json' => \App\Http\Middleware\ForceJsonResponse::class,
    ];
}