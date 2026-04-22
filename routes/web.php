<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourrierController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;

// ========================================================================
// 🌍 Route par défaut (Redirection intelligente)
// ========================================================================
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard.index') 
        : redirect()->route('login');
})->name('home');

// ========================================================================
// 🔓 Routes Publiques (Auth, Inscription, Mots de passe)
// ========================================================================
// Middleware 'guest' : redirige vers dashboard si déjà connecté
// Middleware 'throttle' : protection brute-force (10 req/min)
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    
    // 🖥️ Affichage du formulaire de connexion (GET)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    
    // 🔐 Traitement de la connexion (POST)
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    
    // 📝 Inscription & Récupération MDP
    Route::post('/register',          [LoginController::class, 'register'])->name('register');
    Route::post('/forgot-password',   [LoginController::class, 'forgotPassword'])->name('password.request');
    Route::post('/reset-password',    [LoginController::class, 'resetPassword'])->name('password.update');
});

// ========================================================================
// 🔒 Routes Protégées (Auth + Vérification état compte + CSRF)
// ========================================================================
// 'auth' : vérifie la session
// 'check.active' : vérifie que etat=1 (déconnecte si etat=2)
Route::middleware(['auth', 'check.active'])->group(function () {

    // 🚪 Déconnexion & Profil (disponibles partout dans le groupe protégé)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/me',      [LoginController::class, 'me'])->name('me');

    // 🏠 Dashboard
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/',          [DashboardController::class, 'index'])->name('index');
        Route::get('/overview',  [DashboardController::class, 'overview'])->name('overview');
        Route::post('/refresh',  [DashboardController::class, 'refresh'])->name('refresh');
    });

    // 📬 Courriers
    Route::prefix('courriers')->name('courriers.')->group(function () {
        // ⚠️ Routes spécifiques AVANT /{id} pour éviter les conflits
        Route::get('/stats',              [CourrierController::class, 'stats'])->name('stats');
        Route::get('/api',                [CourrierController::class, 'apiIndex'])->name('api');
        Route::get('/export',             [CourrierController::class, 'export'])->name('export');
        
        Route::post('/{id}/restaurer',    [CourrierController::class, 'restaurer'])->name('restaurer');
        Route::post('/{id}/affecter',     [CourrierController::class, 'affecter'])->name('affecter');
        Route::post('/{id}/traite',       [CourrierController::class, 'marquerTraite'])->name('traite');
        Route::post('/{id}/archiver',     [CourrierController::class, 'archiver'])->name('archiver');

        // CRUD standard
        Route::get('/',                   [CourrierController::class, 'index'])->name('index');
        Route::post('/',                  [CourrierController::class, 'store'])->name('store');
        Route::get('/{id}/edit',          [CourrierController::class, 'edit'])->name('edit');
        
        Route::post('/update/{id}', [CourrierController::class, 'update'])->name('courriers.update');
        Route::get('/show/{id}',               [CourrierController::class, 'show'])->name('show');
        Route::delete('/{id}',            [CourrierController::class, 'destroy'])->name('destroy');
    });

    // 👨‍💼 Agents
    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/stats',              [AgentController::class, 'stats'])->name('stats');
        Route::get('/api',                [AgentController::class, 'apiIndex'])->name('api');
        Route::get('/export',             [AgentController::class, 'export'])->name('export');
        
        Route::post('/{id}/restaurer',    [AgentController::class, 'restaurer'])->name('restaurer');
        Route::post('/{id}/lier-user',    [AgentController::class, 'lierAUser'])->name('lier-user');
        Route::post('/{id}/reassigner-service', [AgentController::class, 'reassignerService'])->name('reassigner-service');

        Route::get('/',                   [AgentController::class, 'index'])->name('index');
        Route::post('/',                  [AgentController::class, 'store'])->name('store');
        Route::get('/{id}/edit',          [AgentController::class, 'edit'])->name('edit');
        Route::put('/{id}',               [AgentController::class, 'update'])->name('update');
        Route::get('/{id}',               [AgentController::class, 'show'])->name('show');
        Route::delete('/{id}',            [AgentController::class, 'destroy'])->name('destroy');
    });


// 🏢 Organisations
Route::prefix('organisations')->name('organisations.')->group(function () {
    
    // ==================================================
    // 📋 Vues principales (existent dans le contrôleur)
    // ==================================================
    Route::get('/', [OrganisationController::class, 'index'])->name('index');
    Route::post('/', [OrganisationController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [OrganisationController::class, 'edit'])->name('edit');
    Route::get('/show/{id}', [OrganisationController::class, 'show'])->name('show');
    Route::put('/{id}', [OrganisationController::class, 'update'])->name('update');
    Route::delete('/{id}', [OrganisationController::class, 'destroy'])->name('destroy');
    
    // ==================================================
    // 🔄 Actions sur le statut (existent dans le contrôleur)
    // ==================================================
    Route::put('/{id}/disable', [OrganisationController::class, 'disable'])->name('disable');
    Route::put('/{id}/enable', [OrganisationController::class, 'enable'])->name('enable');
    Route::post('/{id}/restaurer', [OrganisationController::class, 'restaurer'])->name('restaurer');
    
    // ==================================================
    // 📊 Données et exports (existent dans le contrôleur)
    // ==================================================
    Route::get('/stats', [OrganisationController::class, 'stats'])->name('stats');
    Route::get('/api', [OrganisationController::class, 'apiIndex'])->name('api');
    Route::get('/search', [OrganisationController::class, 'search'])->name('search');
    Route::get('/export', [OrganisationController::class, 'export'])->name('export');
    
    // ==================================================
    // 📧 Courriers associés (existe dans le contrôleur)
    // ==================================================
    Route::get('/{id}/courriers', [OrganisationController::class, 'getCourriers'])->name('courriers');
});

    // 📁 Services
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/stats',                          [ServiceController::class, 'stats'])->name('stats');
        Route::get('/api',                            [ServiceController::class, 'apiIndex'])->name('api');
        Route::get('/export',                         [ServiceController::class, 'export'])->name('export');
        Route::get('/organisation/{organisationId}',  [ServiceController::class, 'getByOrganisation'])->name('by-organisation');
        
        Route::post('/{id}/restaurer',                [ServiceController::class, 'restaurer'])->name('restaurer');

        Route::get('/',                   [ServiceController::class, 'index'])->name('index');
        Route::post('/',                  [ServiceController::class, 'store'])->name('store');
        Route::get('/{id}/edit',          [ServiceController::class, 'edit'])->name('edit');
        Route::put('/{id}',               [ServiceController::class, 'update'])->name('update');
        Route::get('/{id}',               [ServiceController::class, 'show'])->name('show');
        Route::delete('/{id}',            [ServiceController::class, 'destroy'])->name('destroy');
    });

    // 👤 Utilisateurs (🔒 Réservé aux admins)
    Route::prefix('users')->name('users.')->group(function () {
        // Routes protégées par rôle admin
        Route::get('/stats',              [UserController::class, 'stats'])->name('stats')->middleware('role:admin,super_admin');
        Route::get('/api',                [UserController::class, 'apiIndex'])->name('api')->middleware('role:admin,super_admin');
        Route::get('/export',             [UserController::class, 'export'])->name('export')->middleware('role:admin,super_admin');
        
        Route::post('/{id}/restaurer',    [UserController::class, 'restaurer'])->name('restaurer')->middleware('role:admin,super_admin');
        Route::post('/{id}/role',         [UserController::class, 'assignerRole'])->name('role')->middleware('role:admin,super_admin');
        Route::post('/{id}/password',     [UserController::class, 'changerMotDePasse'])->name('password')->middleware('role:admin,super_admin');

        // CRUD utilisateurs (réservé aux admins)
        Route::get('/',                   [UserController::class, 'index'])->name('index')->middleware('role:admin,super_admin');
        Route::post('/',                  [UserController::class, 'store'])->name('store')->middleware('role:admin,super_admin');
        Route::get('/{id}/edit',          [UserController::class, 'edit'])->name('edit')->middleware('role:admin,super_admin');
        Route::put('/{id}',               [UserController::class, 'update'])->name('update')->middleware('role:admin,super_admin');
        Route::get('/{id}',               [UserController::class, 'show'])->name('show')->middleware('role:admin,super_admin');
        Route::delete('/{id}',            [UserController::class, 'destroy'])->name('destroy')->middleware('role:admin,super_admin');
   
   
        });

    // 🧪 Routes de test (DEV ONLY - protégées par check d'environnement dans les contrôleurs)
    if (app()->environment('local')) {
        Route::prefix('test')->name('test.')->group(function () {
            Route::post('/courriers/validation', [CourrierController::class, 'testValidation'])->name('courriers');
            Route::post('/agents/validation',    [AgentController::class, 'testValidation'])->name('agents');
            Route::post('/organisations/validation', [OrganisationController::class, 'testValidation'])->name('organisations');
            Route::post('/services/validation',  [ServiceController::class, 'testValidation'])->name('services');
            Route::post('/users/validation',     [UserController::class, 'testValidation'])->name('users');
        });
    }
});