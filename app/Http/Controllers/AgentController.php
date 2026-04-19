<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentFormRequest;
use App\Services\AgentService;
use App\Models\Agent;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * AgentController
 * Gestion complète des agents : vues Blade + API AJAX + exports + modals
 * Hérite de BaseController pour la gestion unifiée des réponses et erreurs.
 */
class AgentController extends BaseController
{
    public function __construct(protected AgentService $service)
    {
        // Middleware d'autorisation par méthode
        $this->middleware('role:admin,chef_service')->only(['store', 'update', 'destroy', 'restaurer', 'lierAUser', 'reassignerService']);
    }

    // ========================================================================
    // 📄 INDEX : Vue Blade + Données pour DataTables
    // ========================================================================

    public function index(Request $request): View
    {
        $filtres = $request->only(['service_id', 'fonction', 'nom', 'etat', 'search']);
        
        // Données pour la vue (paginées ou filtrées)
        $agents = $this->service->liste($filtres);
        
        // Stats pour les KPI (avec cache 5 min)
        $stats = Cache::remember('agents_stats', 300, fn() => $this->service->getStats());
        
        // Données pour les selects des modals
        $services = Service::where('etat', 1)->orderBy('nom')->get(['id', 'nom']);
        $users = User::where('etat', 1)
            ->whereDoesntHave('agent') // Exclus les users déjà liés à un agent
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('agents.index', compact('agents', 'filtres', 'stats', 'services', 'users'));
    }

    // ========================================================================
    // 📡 API : Données JSON pour DataTables (Server-side processing)
    // ========================================================================

    public function apiIndex(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $query = $this->service->query()
                ->with(['service', 'user'])
                ->where('etat', Agent::ETAT_ACTIF);

            // Recherche globale
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('fonction', 'like', "%{$search}%");
                });
            }

            // Filtres par colonne (DataTables)
            if ($request->filled('columns.2.search.value')) { // Fonction
                $query->where('fonction', $request->input('columns.2.search.value'));
            }
            if ($request->filled('columns.3.search.value')) { // Service
                $query->where('service_id', $request->input('columns.3.search.value'));
            }
            if ($request->filled('columns.5.search.value')) { // État
                $query->where('etat', $request->input('columns.5.search.value'));
            }

            // Tri
            if ($request->filled('order.0.column')) {
                $columnIndex = $request->input('order.0.column');
                $dir = $request->input('order.0.dir', 'asc');
                $columns = ['nom', 'prenom', 'fonction', 'service_id', 'email', 'etat', 'created_at'];
                $query->orderBy($columns[$columnIndex] ?? 'nom', $dir);
            } else {
                $query->orderBy('nom');
            }

            // Pagination
            $total = $query->count();
            $filtered = $request->filled('search.value') 
                ? (clone $query)->count() 
                : $total;
                
            $agents = $query
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 15))
                ->get();

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $agents->map(fn($a) => $this->service->formatAgent($a))
            ]);
        });
    }

    // ========================================================================
    // 📥 STORE : Création (AJAX + Validation)
    // ========================================================================

    public function store(AgentFormRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $validated = $request->validated();
            
            $agent = $this->service->creer($validated);
            
            // Invalider le cache des stats
            Cache::forget('agents_stats');
            
            return $this->respondSuccess(
                'Agent créé avec succès.',
                $this->service->formatAgent($agent),
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
            $agent = Agent::with(['service', 'user'])->findOrFail($id);
            
            // Formatage adapté pour le modal
            $data = [
                'id' => $agent->id,
                'nom' => $agent->nom,
                'prenom' => $agent->prenom,
                'email' => $agent->email,
                'telephone' => $agent->telephone,
                'fonction' => $agent->fonction,
                'service_id' => $agent->service_id,
                'user_id' => $agent->user_id,
                'etat' => $agent->etat,
                'created_at' => $agent->created_at?->format('Y-m-d H:i'),
                'updated_at' => $agent->updated_at?->format('Y-m-d H:i'),
            ];
            
            return $this->respondSuccess('Données récupérées.', $data);
        });
    }

    // ========================================================================
    // ✏️ UPDATE : Modification (AJAX)
    // ========================================================================

    public function update(AgentFormRequest $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $agent = $this->service->mettreAJour($id, $request->validated());
            
            Cache::forget('agents_stats');
            
            return $this->respondSuccess(
                'Agent mis à jour avec succès.',
                $this->service->formatAgent($agent)
            );
        });
    }

    // ========================================================================
    // 👁️ SHOW : Lecture détaillée (JSON)
    // ========================================================================

    public function show(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $agent = Agent::with(['service', 'user'])->findOrFail($id);
            return $this->respondSuccess('Agent récupéré.', $this->service->formatAgent($agent));
        });
    }

    // ========================================================================
    // 🗑️ DESTROY : Suppression logique / Archivage (AJAX)
    // ========================================================================

    public function destroy(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->supprimer($id);
            Cache::forget('agents_stats');
            return $this->respondSuccess('Agent archivé (désactivé) avec succès.');
        });
    }

    // ========================================================================
    // ♻️ RESTAURER : Réactivation (AJAX)
    // ========================================================================

    public function restaurer(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->restaurer($id);
            Cache::forget('agents_stats');
            return $this->respondSuccess('Agent restauré et réactivé avec succès.');
        });
    }

    // ========================================================================
    // 🔗 LIER À USER : Associe un compte Laravel à un agent métier
    // ========================================================================

    public function lierAUser(Request $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $validated = $request->validate([
                'user_id' => [
                    'required',
                    'exists:users,id',
                    // Vérifier que l'user n'est pas déjà lié à un autre agent
                    function($attribute, $value, $fail) use ($id) {
                        $exists = Agent::where('user_id', $value)
                            ->where('id', '!=', $id)
                            ->where('etat', Agent::ETAT_ACTIF)
                            ->exists();
                        if ($exists) {
                            $fail('Cet utilisateur est déjà associé à un autre agent.');
                        }
                    },
                ],
            ]);

            $this->service->lierAUser($id, $validated['user_id']);
            Cache::forget('agents_stats');
            
            return $this->respondSuccess('Compte utilisateur associé à l\'agent avec succès.');
        });
    }

    // ========================================================================
    // 🔄 RÉASSIGNER SERVICE : Change le service de rattachement
    // ========================================================================

    public function reassignerService(Request $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
            ]);

            $this->service->reassignerService($id, $validated['service_id']);
            Cache::forget('agents_stats');
            
            return $this->respondSuccess('Service de rattachement modifié avec succès.');
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
                ->with(['service', 'user'])
                ->where('etat', Agent::ETAT_ACTIF);
            
            // Appliquer les filtres
            if (!empty($filters['fonction'])) $query->where('fonction', $filters['fonction']);
            if (!empty($filters['service'])) $query->where('service_id', $filters['service']);
            if (!empty($filters['etat'])) $query->where('etat', $filters['etat']);
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('nom', 'like', "%{$filters['search']}%")
                      ->orWhere('prenom', 'like', "%{$filters['search']}%")
                      ->orWhere('email', 'like', "%{$filters['search']}%");
                });
            }
            
            $agents = $query->orderBy('nom')->get();
            
            return match($format) {
                'pdf' => $this->exportPDF($agents),
                'csv' => $this->exportCSV($agents),
                default => $this->exportExcel($agents),
            };
        });
    }

    // ── Helpers d'export ──
    
    protected function exportExcel($agents): StreamedResponse
    {
        return response()->streamDownload(function() use ($agents) {
            $output = fopen('php://output', 'w');
            
            // En-têtes CSV avec séparateur point-virgule (Excel FR)
            fputcsv($output, [
                'Nom', 'Prénom', 'Email', 'Téléphone', 'Fonction',
                'Service', 'Organisation', 'Compte lié', 'État', 'Date création'
            ], ';');
            
            // Données
            foreach ($agents as $a) {
                fputcsv($output, [
                    $a->nom ?? '—',
                    $a->prenom ?? '—',
                    $a->email ?? '—',
                    $a->telephone ?? '—',
                    $this->getFunctionLabel($a->fonction),
                    $a->service?->nom ?? '—',
                    $a->service?->organisation?->nom ?? '—',
                    $a->user ? 'Oui' : 'Non',
                    $a->etat == 1 ? 'Actif' : 'Inactif',
                    $a->created_at?->format('d/m/Y H:i') ?? '—',
                ], ';');
            }
            fclose($output);
        }, 'agents_export_'.date('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function exportCSV($agents): StreamedResponse
    {
        return $this->exportExcel($agents); // Même logique, extension différente
    }

    protected function exportPDF($agents): StreamedResponse
    {
        // Si tu utilises dompdf ou snappy :
        // $pdf = \PDF::loadView('agents.export-pdf', compact('agents'));
        // return $pdf->download('agents_'.date('Y-m-d').'.pdf');
        
        // Fallback : rediriger vers Excel si PDF non configuré
        return $this->exportExcel($agents);
    }

    protected function getFunctionLabel(?string $fonction): string
    {
        return match($fonction) {
            'chef' => 'Chef de service',
            'secretaire' => 'Secrétaire',
            'gestionnaire' => 'Gestionnaire courrier',
            'agent' => 'Agent de saisie',
            'charge_mission' => 'Chargé de mission',
            default => '—',
        };
    }

    // ========================================================================
    // 📊 STATS : Dashboard (JSON avec cache)
    // ========================================================================

    public function stats(): JsonResponse
    {
        return $this->execute(function () {
            $stats = Cache::remember('agents_stats', 300, fn() => $this->service->getStats());
            return $this->respondSuccess('Statistiques chargées.', $stats);
        });
    }

    // ========================================================================
    // 🗂️ UTILITAIRES DEV
    // ========================================================================

    /**
     * Endpoint de test pour validation upload (dev only)
     */
    public function testUpload(Request $request): JsonResponse
    {
        if (!app()->environment('local')) {
            return $this->respondError('Endpoint réservé au développement.', [], 403);
        }

        $request->validate(['avatar' => 'nullable|file|mimes:jpg,jpeg,png|max:2048']);
        
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('test-avatars', 'public');
            return $this->respondSuccess('Avatar uploadé avec succès.', [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ]);
        }
        
        return $this->respondSuccess('Aucun fichier envoyé.');
    }
}