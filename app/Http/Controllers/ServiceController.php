<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceFormRequest;
use App\Services\ServiceService;
use App\Models\Service;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ServiceController
 * Gestion complète des services : vues Blade + API AJAX + exports + modals
 * Hérite de BaseController pour la gestion unifiée des réponses et erreurs.
 */
class ServiceController extends BaseController
{
    public function __construct(protected ServiceService $service)
    {
        // Middleware d'autorisation par méthode
        $this->middleware('role:admin,super_admin')->only(['store', 'update', 'destroy', 'restaurer']);
    }

    // ========================================================================
    // 📄 INDEX : Vue Blade + Données pour DataTables
    // ========================================================================

    public function index(Request $request): View
    {
        $filtres = $request->only(['organisation_id', 'nom', 'etat', 'search']);
        
        // Données pour la vue (paginées ou filtrées)
        $services = $this->service->liste($filtres);
        
        // Stats pour les KPI (avec cache 5 min)
        $stats = Cache::remember('services_stats', 300, fn() => $this->service->getStats());
        
        // Données pour les selects des modals
        $organisations = Organisation::where('etat', 1)->orderBy('nom')->get(['id', 'nom', 'sigle']);
        
        return view('services.index', compact('services', 'filtres', 'stats', 'organisations'));
    }

    // ========================================================================
    // 📡 API : Données JSON pour DataTables (Server-side processing)
    // ========================================================================

    public function apiIndex(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $query = $this->service->query()
                ->with(['organisation'])
                ->withCount('agents')
                ->where('etat', Service::ETAT_ACTIF);

            // Recherche globale
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhereHas('organisation', fn($o) => $o->where('nom', 'like', "%{$search}%")
                        ->orWhere('sigle', 'like', "%{$search}%"));
                });
            }

            // Filtres par colonne (DataTables)
            if ($request->filled('columns.1.search.value')) { // Organisation
                $query->where('organisation_id', $request->input('columns.1.search.value'));
            }
            if ($request->filled('columns.3.search.value')) { // État
                $query->where('etat', $request->input('columns.3.search.value'));
            }

            // Tri
            if ($request->filled('order.0.column')) {
                $columnIndex = $request->input('order.0.column');
                $dir = $request->input('order.0.dir', 'asc');
                $columns = ['nom', 'organisation_id', 'created_at', 'etat'];
                $query->orderBy($columns[$columnIndex] ?? 'nom', $dir);
            } else {
                $query->orderBy('nom');
            }

            // Pagination
            $total = $query->count();
            $filtered = $request->filled('search.value') 
                ? (clone $query)->count() 
                : $total;
                
            $services = $query
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 15))
                ->get();

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $services->map(fn($s) => $this->service->formatService($s))
            ]);
        });
    }

    // ========================================================================
    // 📥 STORE : Création (AJAX + Validation)
    // ========================================================================

    public function store(ServiceFormRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $validated = $request->validated();
            
            $service = $this->service->creer($validated);
            
            // Invalider le cache des stats
            Cache::forget('services_stats');
            
            return $this->respondSuccess(
                'Service créé avec succès.',
                $this->service->formatService($service),
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
            $service = Service::findOrFail($id);
            
            // Formatage adapté pour le modal
            $data = [
                'id' => $service->id,
                'nom' => $service->nom,
                'organisation_id' => $service->organisation_id,
                'etat' => $service->etat,
                'created_at' => $service->created_at?->format('Y-m-d H:i'),
                'updated_at' => $service->updated_at?->format('Y-m-d H:i'),
            ];
            
            return $this->respondSuccess('Données récupérées.', $data);
        });
    }

    // ========================================================================
    // ✏️ UPDATE : Modification (AJAX)
    // ========================================================================

    public function update(ServiceFormRequest $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $service = $this->service->mettreAJour($id, $request->validated());
            
            Cache::forget('services_stats');
            
            return $this->respondSuccess(
                'Service mis à jour avec succès.',
                $this->service->formatService($service)
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
            // Pour inclure les archives: Service::withoutGlobalScope('actif')->findOrFail($id)
            $service = Service::with(['organisation'])->withCount('agents')->findOrFail($id);
            return $this->respondSuccess('Service récupéré.', $this->service->formatService($service));
        });
    }

    // ========================================================================
    // 🗑️ DESTROY : Suppression logique / Archivage (AJAX)
    // ========================================================================

    public function destroy(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->supprimer($id);
            Cache::forget('services_stats');
            return $this->respondSuccess('Service désactivé (archivé) avec succès.');
        });
    }

    // ========================================================================
    // ♻️ RESTAURER : Réactivation (AJAX)
    // ========================================================================

    public function restaurer(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->restaurer($id);
            Cache::forget('services_stats');
            return $this->respondSuccess('Service réactivé avec succès.');
        });
    }

    // ========================================================================
    // 🏢 PAR ORGANISATION : Récupère les services d'une organisation (AJAX)
    // ========================================================================

    public function getByOrganisation(int $organisationId): JsonResponse
    {
        return $this->execute(function () use ($organisationId) {
            $services = $this->service->getByOrganisation($organisationId);
            return $this->respondSuccess('Services récupérés.', $services);
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
                ->with(['organisation'])
                ->withCount('agents')
                ->where('etat', Service::ETAT_ACTIF);
            
            // Appliquer les filtres
            if (!empty($filters['organisation'])) $query->where('organisation_id', $filters['organisation']);
            if (!empty($filters['etat'])) $query->where('etat', $filters['etat']);
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('nom', 'like', "%{$filters['search']}%")
                      ->orWhereHas('organisation', fn($o) => $o->where('nom', 'like', "%{$filters['search']}%"));
                });
            }
            
            $services = $query->orderBy('nom')->get();
            
            return match($format) {
                'pdf' => $this->exportPDF($services),
                'csv' => $this->exportCSV($services),
                default => $this->exportExcel($services),
            };
        });
    }

    // ── Helpers d'export ──
    
    protected function exportExcel($services): StreamedResponse
    {
        return response()->streamDownload(function() use ($services) {
            $output = fopen('php://output', 'w');
            
            // En-têtes CSV avec séparateur point-virgule (Excel FR)
            fputcsv($output, [
                'Nom du service', 'Organisation', 'Sigle Orga', 
                'Agents affectés', 'État', 'Date création'
            ], ';');
            
            // Données
            foreach ($services as $s) {
                fputcsv($output, [
                    $s->nom ?? '—',
                    $s->organisation?->nom ?? '—',
                    $s->organisation?->sigle ?? '—',
                    $s->agents_count ?? 0,
                    $s->etat == 1 ? 'Actif' : 'Inactif',
                    $s->created_at?->format('d/m/Y H:i') ?? '—',
                ], ';');
            }
            fclose($output);
        }, 'services_export_'.date('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function exportCSV($services): StreamedResponse
    {
        return $this->exportExcel($services); // Même logique, extension différente
    }

    protected function exportPDF($services): StreamedResponse
    {
        // Si tu utilises dompdf ou snappy :
        // $pdf = \PDF::loadView('services.export-pdf', compact('services'));
        // return $pdf->download('services_'.date('Y-m-d').'.pdf');
        
        // Fallback : rediriger vers Excel si PDF non configuré
        return $this->exportExcel($services);
    }

    // ========================================================================
    // 📊 STATS : Dashboard (JSON avec cache)
    // ========================================================================

    public function stats(): JsonResponse
    {
        return $this->execute(function () {
            $stats = Cache::remember('services_stats', 300, fn() => $this->service->getStats());
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
            'nom' => 'required|string|max:150',
            'organisation_id' => 'required|exists:organisations,id',
        ]);
        
        return $this->respondSuccess('Validation réussie.', $request->validated());
    }
}