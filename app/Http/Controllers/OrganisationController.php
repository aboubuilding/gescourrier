<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationFormRequest;
use App\Services\OrganisationService;
use App\Models\Organisation;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * OrganisationController
 * Gestion complète des organisations : vues Blade + API AJAX + exports + modals
 * Hérite de BaseController pour la gestion unifiée des réponses et erreurs.
 */
class OrganisationController extends BaseController
{
    public function __construct(protected OrganisationService $service)
    {
        // Middleware d'autorisation par méthode
        $this->middleware('role:admin,super_admin')->only(['store', 'update', 'destroy', 'restaurer']);
    }

    // ========================================================================
    // 📄 INDEX : Vue Blade + Données pour DataTables
    // ========================================================================

    public function index(Request $request): View
    {
        $filtres = $request->only(['type', 'etat', 'nom', 'sigle', 'search']);
        
        // Données pour la vue (paginées ou filtrées)
        $organisations = $this->service->liste($filtres);
        
        // Stats pour les KPI (avec cache 5 min)
        $stats = Cache::remember('organisations_stats', 300, fn() => $this->service->getStats());
        
        return view('organisations.index', compact('organisations', 'filtres', 'stats'));
    }

    // ========================================================================
    // 📡 API : Données JSON pour DataTables (Server-side processing)
    // ========================================================================

    public function apiIndex(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $query = $this->service->query()
                ->withCount('services')
                ->where('etat', Organisation::ETAT_ACTIF);

            // Recherche globale
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('sigle', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('adresse', 'like', "%{$search}%");
                });
            }

            // Filtres par colonne (DataTables)
            if ($request->filled('columns.2.search.value')) { // Type
                $query->where('type', $request->input('columns.2.search.value'));
            }
            if ($request->filled('columns.4.search.value')) { // État
                $query->where('etat', $request->input('columns.4.search.value'));
            }

            // Tri
            if ($request->filled('order.0.column')) {
                $columnIndex = $request->input('order.0.column');
                $dir = $request->input('order.0.dir', 'asc');
                $columns = ['nom', 'sigle', 'type', 'email', 'etat', 'created_at'];
                $query->orderBy($columns[$columnIndex] ?? 'nom', $dir);
            } else {
                $query->orderBy('nom');
            }

            // Pagination
            $total = $query->count();
            $filtered = $request->filled('search.value') 
                ? (clone $query)->count() 
                : $total;
                
            $organisations = $query
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 15))
                ->get();

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $organisations->map(fn($o) => $this->service->formatOrganisation($o))
            ]);
        });
    }

    // ========================================================================
    // 📥 STORE : Création (AJAX + Validation)
    // ========================================================================

    public function store(OrganisationFormRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $validated = $request->validated();
            
            $org = $this->service->creer($validated);
            
            // Invalider le cache des stats
            Cache::forget('organisations_stats');
            
            return $this->respondSuccess(
                'Organisation créée avec succès.',
                $this->service->formatOrganisation($org),
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
            $org = Organisation::findOrFail($id);
            
            // Formatage adapté pour le modal
            $data = [
                'id' => $org->id,
                'nom' => $org->nom,
                'sigle' => $org->sigle,
                'type' => $org->type,
                'adresse' => $org->adresse,
                'telephone' => $org->telephone,
                'email' => $org->email,
                'etat' => $org->etat,
                'created_at' => $org->created_at?->format('Y-m-d H:i'),
                'updated_at' => $org->updated_at?->format('Y-m-d H:i'),
            ];
            
            return $this->respondSuccess('Données récupérées.', $data);
        });
    }

    // ========================================================================
    // ✏️ UPDATE : Modification (AJAX)
    // ========================================================================

    public function update(OrganisationFormRequest $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $org = $this->service->mettreAJour($id, $request->validated());
            
            Cache::forget('organisations_stats');
            
            return $this->respondSuccess(
                'Organisation mise à jour avec succès.',
                $this->service->formatOrganisation($org)
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
            // Pour inclure les archives: Organisation::withoutGlobalScope('actif')->findOrFail($id)
            $org = Organisation::withCount('services')->findOrFail($id);
            return $this->respondSuccess('Organisation récupérée.', $this->service->formatOrganisation($org));
        });
    }

    // ========================================================================
    // 🗑️ DESTROY : Suppression logique / Archivage (AJAX)
    // ========================================================================

    public function destroy(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->supprimer($id);
            Cache::forget('organisations_stats');
            return $this->respondSuccess('Organisation désactivée (archivée) avec succès.');
        });
    }

    // ========================================================================
    // ♻️ RESTAURER : Réactivation (AJAX)
    // ========================================================================

    public function restaurer(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->restaurer($id);
            Cache::forget('organisations_stats');
            return $this->respondSuccess('Organisation réactivée avec succès.');
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
                ->withCount('services')
                ->where('etat', Organisation::ETAT_ACTIF);
            
            // Appliquer les filtres
            if (!empty($filters['type'])) $query->where('type', $filters['type']);
            if (!empty($filters['etat'])) $query->where('etat', $filters['etat']);
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('nom', 'like', "%{$filters['search']}%")
                      ->orWhere('sigle', 'like', "%{$filters['search']}%")
                      ->orWhere('email', 'like', "%{$filters['search']}%");
                });
            }
            
            $organisations = $query->orderBy('nom')->get();
            
            return match($format) {
                'pdf' => $this->exportPDF($organisations),
                'csv' => $this->exportCSV($organisations),
                default => $this->exportExcel($organisations),
            };
        });
    }

    // ── Helpers d'export ──
    
    protected function exportExcel($organisations): StreamedResponse
    {
        return response()->streamDownload(function() use ($organisations) {
            $output = fopen('php://output', 'w');
            
            // En-têtes CSV avec séparateur point-virgule (Excel FR)
            fputcsv($output, [
                'Nom', 'Sigle', 'Type', 'Email', 'Téléphone', 
                'Adresse', 'Services liés', 'État', 'Date création'
            ], ';');
            
            // Données
            foreach ($organisations as $o) {
                fputcsv($output, [
                    $o->nom ?? '—',
                    $o->sigle ?? '—',
                    $this->getTypeLabel($o->type),
                    $o->email ?? '—',
                    $o->telephone ?? '—',
                    $o->adresse ?? '—',
                    $o->services_count ?? 0,
                    $o->etat == 1 ? 'Actif' : 'Inactif',
                    $o->created_at?->format('d/m/Y H:i') ?? '—',
                ], ';');
            }
            fclose($output);
        }, 'organisations_export_'.date('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function exportCSV($organisations): StreamedResponse
    {
        return $this->exportExcel($organisations); // Même logique, extension différente
    }

    protected function exportPDF($organisations): StreamedResponse
    {
        // Si tu utilises dompdf ou snappy :
        // $pdf = \PDF::loadView('organisations.export-pdf', compact('organisations'));
        // return $pdf->download('organisations_'.date('Y-m-d').'.pdf');
        
        // Fallback : rediriger vers Excel si PDF non configuré
        return $this->exportExcel($organisations);
    }

    protected function getTypeLabel(?int $type): string
    {
        return match($type) {
            0 => 'Externe',
            1 => 'Interne',
            2 => 'Gouvernementale',
            3 => 'Privée',
            4 => 'ONG',
            default => '—',
        };
    }

    // ========================================================================
    // 📊 STATS : Dashboard (JSON avec cache)
    // ========================================================================

    public function stats(): JsonResponse
    {
        return $this->execute(function () {
            $stats = Cache::remember('organisations_stats', 300, fn() => $this->service->getStats());
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
            'sigle' => 'nullable|string|max:20|unique:organisations,sigle',
            'type' => 'required|integer|in:0,1,2,3,4',
        ]);
        
        return $this->respondSuccess('Validation réussie.', $request->validated());
    }
}