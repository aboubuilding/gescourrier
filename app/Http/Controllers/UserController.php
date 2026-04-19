<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * UserController
 * Gestion complète des utilisateurs : vues Blade + API AJAX + exports + modals
 * Hérite de BaseController pour la gestion unifiée des réponses et erreurs.
 */
class UserController extends BaseController
{
    public function __construct(protected UserService $service)
    {
        // Middleware d'autorisation par méthode
        $this->middleware('role:admin,super_admin')->only(['store', 'update', 'destroy', 'restaurer', 'assignerRole', 'changerMotDePasse']);
    }

    // ========================================================================
    // 📄 INDEX : Vue Blade + Données pour DataTables
    // ========================================================================

    public function index(Request $request): View
    {
        $filtres = $request->only(['role', 'etat', 'nom', 'email', 'search']);
        
        // Données pour la vue (paginées ou filtrées)
        $users = $this->service->liste($filtres);
        
        // Stats pour les KPI (avec cache 5 min)
        $stats = Cache::remember('users_stats', 300, fn() => $this->service->getStats());
        
        return view('users.index', compact('users', 'filtres', 'stats'));
    }

    // ========================================================================
    // 📡 API : Données JSON pour DataTables (Server-side processing)
    // ========================================================================

    public function apiIndex(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $query = $this->service->query()
                ->where('etat', User::ETAT_ACTIF);

            // Recherche globale
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
                });
            }

            // Filtres par colonne (DataTables)
            if ($request->filled('columns.1.search.value')) { // Rôle
                $query->where('role', $request->input('columns.1.search.value'));
            }
            if ($request->filled('columns.4.search.value')) { // État
                $query->where('etat', $request->input('columns.4.search.value'));
            }

            // Tri
            if ($request->filled('order.0.column')) {
                $columnIndex = $request->input('order.0.column');
                $dir = $request->input('order.0.dir', 'asc');
                $columns = ['name', 'role', 'email', 'derniere_connexion', 'etat', 'created_at'];
                $query->orderBy($columns[$columnIndex] ?? 'name', $dir);
            } else {
                $query->orderBy('name');
            }

            // Pagination
            $total = $query->count();
            $filtered = $request->filled('search.value') 
                ? (clone $query)->count() 
                : $total;
                
            $users = $query
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 15))
                ->get();

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $users->map(fn($u) => $this->service->formatUser($u))
            ]);
        });
    }

    // ========================================================================
    // 📥 STORE : Création (AJAX + Validation)
    // ========================================================================

    public function store(UserFormRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $validated = $request->validated();
            
            $user = $this->service->creer($validated);
            
            // Invalider le cache des stats
            Cache::forget('users_stats');
            
            return $this->respondSuccess(
                'Utilisateur créé avec succès.',
                $this->service->formatUser($user),
                201
            );
        });
    }

    // ========================================================================
    // ✏️ EDIT : Pré-remplissage du modal modification (JSON)
    // ========================================================================

    public function edit(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $user = User::findOrFail($id);
            
            // Formatage adapté pour le modal
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'telephone' => $user->telephone,
                'etat' => $user->etat,
                'email_verified_at' => $user->email_verified_at?->format('Y-m-d H:i'),
                'derniere_connexion' => $user->derniere_connexion?->format('Y-m-d H:i'),
                'created_at' => $user->created_at?->format('Y-m-d H:i'),
                'updated_at' => $user->updated_at?->format('Y-m-d H:i'),
            ];
            
            return $this->respondSuccess('Données récupérées.', $data);
        });
    }

    // ========================================================================
    // ✏️ UPDATE : Modification (AJAX)
    // ========================================================================

    public function update(UserFormRequest $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $user = $this->service->mettreAJour($id, $request->validated());
            
            Cache::forget('users_stats');
            
            return $this->respondSuccess(
                'Utilisateur mis à jour avec succès.',
                $this->service->formatUser($user)
            );
        });
    }

    // ========================================================================
    // 👁️ SHOW : Lecture détaillée (JSON)
    // ========================================================================

    public function show(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            // Note: Le scope global 'actif' s'applique par défaut
            // Pour inclure les archives: User::withoutGlobalScope('actif')->findOrFail($id)
            $user = User::findOrFail($id);
            return $this->respondSuccess('Utilisateur récupéré.', $this->service->formatUser($user));
        });
    }

    // ========================================================================
    // 🗑️ DESTROY : Suspension logique (AJAX)
    // ========================================================================

    public function destroy(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->supprimer($id);
            Cache::forget('users_stats');
            return $this->respondSuccess('Utilisateur suspendu (désactivé) avec succès.');
        });
    }

    // ========================================================================
    // ♻️ RESTAURER : Réactivation (AJAX)
    // ========================================================================

    public function restaurer(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->restaurer($id);
            Cache::forget('users_stats');
            return $this->respondSuccess('Utilisateur réactivé avec succès.');
        });
    }

    // ========================================================================
    // 🔑 CHANGER MOT DE PASSE (AJAX)
    // ========================================================================

    public function changerMotDePasse(Request $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $this->service->changerMotDePasse($id, $validated['password']);
            
            return $this->respondSuccess('Mot de passe modifié avec succès.');
        });
    }

    // ========================================================================
    // 🛡️ ASSIGNER RÔLE (AJAX)
    // ========================================================================

    public function assignerRole(Request $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $validated = $request->validate([
                'role' => 'required|string|in:admin,chef_service,secretaire,agent',
            ]);

            // Sécurité : empêcher l'auto-promotion ou modification de super_admin
            if (auth()->id() === $id && !in_array(auth()->user()->role, ['super_admin'])) {
                return $this->respondError('Vous ne pouvez pas modifier votre propre rôle.', [], 403);
            }
            
            // Sécurité : empêcher la création d'un super_admin par un admin simple
            if ($validated['role'] === 'super_admin' && auth()->user()->role !== 'super_admin') {
                return $this->respondError('Seul un super_admin peut attribuer ce rôle.', [], 403);
            }

            $this->service->assignerRole($id, $validated['role']);
            Cache::forget('users_stats');
            
            return $this->respondSuccess('Rôle attribué avec succès.');
        });
    }

    // ========================================================================
    // 📤 EXPORT : Excel / CSV / PDF
    // ========================================================================

    public function export(Request $request): StreamedResponse
    {
        return $this->execute(function () use ($request) {
            $format = $request->get('format', 'xlsx');
            $filters = json_decode($request->get('filters', '{}'), true);
            
            // Récupérer les données filtrées
            $query = $this->service->query()
                ->where('etat', User::ETAT_ACTIF);
            
            // Appliquer les filtres
            if (!empty($filters['role'])) $query->where('role', $filters['role']);
            if (!empty($filters['etat'])) $query->where('etat', $filters['etat']);
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                      ->orWhere('email', 'like', "%{$filters['search']}%")
                      ->orWhere('telephone', 'like', "%{$filters['search']}%");
                });
            }
            
            $users = $query->orderBy('name')->get();
            
            return match($format) {
                'pdf' => $this->exportPDF($users),
                'csv' => $this->exportCSV($users),
                default => $this->exportExcel($users),
            };
        });
    }

    // ── Helpers d'export ──
    
    protected function exportExcel($users): StreamedResponse
    {
        return response()->streamDownload(function() use ($users) {
            $output = fopen('php://output', 'w');
            
            // En-têtes CSV avec séparateur point-virgule (Excel FR)
            fputcsv($output, [
                'Nom', 'Email', 'Rôle', 'Téléphone', 'Email vérifié', 
                'État', 'Dernière connexion', 'Date création'
            ], ';');
            
            // Données
            foreach ($users as $u) {
                fputcsv($output, [
                    $u->name ?? '—',
                    $u->email ?? '—',
                    $this->getRoleLabel($u->role),
                    $u->telephone ?? '—',
                    $u->email_verified_at ? 'Oui' : 'Non',
                    $u->etat == 1 ? 'Actif' : 'Suspendu',
                    $u->derniere_connexion?->format('d/m/Y H:i') ?? 'Jamais',
                    $u->created_at?->format('d/m/Y H:i') ?? '—',
                ], ';');
            }
            fclose($output);
        }, 'users_export_'.date('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function exportCSV($users): StreamedResponse
    {
        return $this->exportExcel($users); // Même logique, extension différente
    }

    protected function exportPDF($users): StreamedResponse
    {
        // Si tu utilises dompdf ou snappy :
        // $pdf = \PDF::loadView('users.export-pdf', compact('users'));
        // return $pdf->download('users_'.date('Y-m-d').'.pdf');
        
        // Fallback : rediriger vers Excel si PDF non configuré
        return $this->exportExcel($users);
    }

    protected function getRoleLabel(?string $role): string
    {
        return match($role) {
            'admin' => 'Administrateur',
            'chef_service' => 'Chef de service',
            'secretaire' => 'Secrétaire',
            'agent' => 'Agent',
            'super_admin' => 'Super Administrateur',
            default => 'Utilisateur',
        };
    }

    // ========================================================================
    // 📊 STATS : Dashboard (JSON avec cache)
    // ========================================================================

    public function stats(): JsonResponse
    {
        return $this->execute(function () {
            $stats = Cache::remember('users_stats', 300, fn() => $this->service->getStats());
            return $this->respondSuccess('Statistiques chargées.', $stats);
        });
    }

    // ========================================================================
    // 🗂️ UTILITAIRES DEV
    // ========================================================================

    /**
     * Endpoint de test pour validation (dev only)
     */
    public function testValidation(Request $request): JsonResponse
    {
        if (!app()->environment('local')) {
            return $this->respondError('Endpoint réservé au développement.', [], 403);
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'role' => 'required|string|in:admin,chef_service,secretaire,agent',
            'password' => 'required|string|min:8',
        ]);
        
        return $this->respondSuccess('Validation réussie.', $request->validated());
    }
}