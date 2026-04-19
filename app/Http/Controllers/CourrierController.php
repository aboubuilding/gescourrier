<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourrierFormRequest;
use App\Services\CourrierService;
use App\Models\Courrier;
use App\Models\Service;
use App\Models\Organisation;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourrierController extends BaseController
{
    public function __construct(protected CourrierService $service)
    {
        // Middleware d'autorisation par méthode (optionnel)
        $this->middleware('role:admin,chef_service,secretaire')->only(['store', 'update', 'destroy', 'restaurer']);
        $this->middleware('role:admin,chef_service')->only(['affecter', 'marquerTraite']);
    }

    // ========================================================================
    // 📄 INDEX : Vue Blade + Données pour DataTables
    // ========================================================================

    public function index(Request $request): View
    {
        // Récupération des filtres depuis la requête
        $filtres = $request->only(['type', 'priorite', 'statut', 'service_id', 'search']);
        
        // Données pour la vue (paginées côté serveur si besoin)
        $courriers = $this->service->liste($filtres);
        
        // Stats pour les KPI (avec cache 5 min)
        $stats = Cache::remember('courriers_stats', 300, fn() => $this->service->getStats());
        
        // Données pour les selects des modals
        $services = Service::where('etat', 1)->orderBy('nom')->get(['id', 'nom']);
        $organisations = Organisation::where('etat', 1)->orderBy('nom')->get(['id', 'nom', 'sigle']);
        $agents = Agent::with('user')->where('etat', 1)->get(['id', 'nom', 'prenom', 'fonction', 'user_id']);

        return view('courriers.index', compact(
            'courriers', 'filtres', 'stats', 'services', 'organisations', 'agents'
        ));
    }

    // ========================================================================
    // 📡 API : Données JSON pour DataTables (Server-side processing optionnel)
    // ========================================================================

    public function apiIndex(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $query = $this->service->query()
                ->with(['service', 'organisation', 'agent.user'])
                ->where('etat', Courrier::ETAT_ACTIF);

            // Recherche globale
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                      ->orWhere('objet', 'like', "%{$search}%")
                      ->orWhere('expediteur', 'like', "%{$search}%")
                      ->orWhere('destinataire', 'like', "%{$search}%");
                });
            }

            // Filtres par colonne (DataTables)
            if ($request->filled('columns.2.search.value')) { // Type
                $query->where('type', $request->input('columns.2.search.value'));
            }
            if ($request->filled('columns.3.search.value')) { // Priorité
                $query->where('priorite', $request->input('columns.3.search.value'));
            }
            if ($request->filled('columns.5.search.value')) { // Statut
                $query->where('statut', $request->input('columns.5.search.value'));
            }

            // Tri
            if ($request->filled('order.0.column')) {
                $columnIndex = $request->input('order.0.column');
                $dir = $request->input('order.0.dir', 'asc');
                $columns = ['reference', 'objet', 'type', 'priorite', 'service_id', 'statut', 'created_at'];
                $query->orderBy($columns[$columnIndex] ?? 'created_at', $dir);
            } else {
                $query->latest('date_reception');
            }

            // Pagination
            $total = $query->count();
            $filtered = $request->filled('search.value') 
                ? (clone $query)->count() 
                : $total;
                
            $courriers = $query
                ->offset($request->input('start', 0))
                ->limit($request->input('length', 15))
                ->get();

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $courriers->map(fn($c) => $this->service->formatCourrier($c))
            ]);
        });
    }

    // ========================================================================
    // 📥 STORE : Création (AJAX + Validation)
    // ========================================================================

    public function store(CourrierFormRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $validated = $request->validated();
            
            // Gestion du fichier scanné
            $fichier = $request->file('fichier');
            
            $courrier = $this->service->creer($validated, $fichier);
            
            // Invalider le cache des stats
            Cache::forget('courriers_stats');
            
            return $this->respondSuccess(
                'Courrier créé avec succès.',
                $this->service->formatCourrier($courrier),
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
            $courrier = Courrier::with(['service', 'organisation'])->findOrFail($id);
            
            // Formatage adapté pour le modal
            $data = [
                'id' => $courrier->id,
                'reference' => $courrier->reference,
                'numero' => $courrier->numero,
                'type' => $courrier->type,
                'priorite' => $courrier->priorite,
                'statut' => $courrier->statut,
                'objet' => $courrier->objet,
                'description' => $courrier->description,
                'expediteur' => $courrier->expediteur,
                'destinataire' => $courrier->destinataire,
                'date_reception' => $courrier->date_reception?->format('Y-m-d'),
                'date_envoi' => $courrier->date_envoi?->format('Y-m-d'),
                'service_id' => $courrier->service_id,
                'organisation_id' => $courrier->organisation_id,
                'agent_id' => $courrier->agent_id,
                'fichier_nom_original' => $courrier->fichier_nom_original,
                'fichier_url' => $courrier->url_fichier ? Storage::disk('public')->url($courrier->url_fichier) : null,
            ];
            
            return $this->respondSuccess('Données récupérées.', $data);
        });
    }

    // ========================================================================
    // ✏️ UPDATE : Modification (AJAX + Fichier optionnel)
    // ========================================================================

    public function update(CourrierFormRequest $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            // Gestion du fichier si uploadé
            if ($request->hasFile('fichier')) {
                // Supprimer l'ancien fichier si existe
                $ancien = Courrier::findOrFail($id);
                if ($ancien->url_fichier && Storage::disk('public')->exists($ancien->url_fichier)) {
                    Storage::disk('public')->delete($ancien->url_fichier);
                }
                $this->service->attacherFichier($id, $request->file('fichier'));
            }

            $courrier = $this->service->mettreAJour($id, $request->validated());
            
            Cache::forget('courriers_stats');
            
            return $this->respondSuccess(
                'Courrier mis à jour avec succès.',
                $this->service->formatCourrier($courrier)
            );
        });
    }

    // ========================================================================
    // 👁️ SHOW : Lecture détaillée (JSON)
    // ========================================================================

    public function show(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $courrier = Courrier::with(['service', 'organisation', 'agent.user'])->findOrFail($id);
            return $this->respondSuccess('Courrier récupéré.', $this->service->formatCourrier($courrier));
        });
    }

    // ========================================================================
    // 🗑️ DESTROY : Suppression logique (AJAX)
    // ========================================================================

    public function destroy(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $courrier = Courrier::findOrFail($id);
            
            // Supprimer le fichier associé si existe
            if ($courrier->url_fichier && Storage::disk('public')->exists($courrier->url_fichier)) {
                Storage::disk('public')->delete($courrier->url_fichier);
            }
            
            $this->service->supprimer($id);
            Cache::forget('courriers_stats');
            
            return $this->respondSuccess('Courrier archivé avec succès.');
        });
    }

    // ========================================================================
    // ♻️ RESTAURER : Remise en état actif (AJAX)
    // ========================================================================

    public function restaurer(int $id): JsonResponse
    {
        return $this->execute(function () use ($id) {
            $this->service->restaurer($id);
            Cache::forget('courriers_stats');
            return $this->respondSuccess('Courrier restauré avec succès.');
        });
    }

    // ========================================================================
    // 📦 ARCHIVER : Alias pour supprimer (logique métier)
    // ========================================================================

    public function archiver(int $id): JsonResponse
    {
        return $this->destroy($id); // Réutilise la logique de suppression logique
    }

    // ========================================================================
    // 👤 AFFECTER : Affectation à un agent & service (AJAX)
    // ========================================================================

    public function affecter(Request $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $validated = $request->validate([
                'agent_id'   => 'required|exists:users,id',
                'service_id' => 'required|exists:services,id',
                'note'       => 'nullable|string|max:500',
            ]);

            $this->service->affecter($id, $validated['agent_id'], $validated['service_id']);
            Cache::forget('courriers_stats');
            
            return $this->respondSuccess('Courrier affecté avec succès.');
        });
    }

    // ========================================================================
    // ✅ MARQUER TRAITÉ : Workflow métier (AJAX)
    // ========================================================================

    public function marquerTraite(Request $request, int $id): JsonResponse
    {
        return $this->execute(function () use ($request, $id) {
            $userId = $request->user()?->id;
            if (!$userId) {
                return $this->respondError('Authentification requise.', [], 401);
            }

            $this->service->marquerTraite($id, $userId);
            Cache::forget('courriers_stats');
            
            return $this->respondSuccess('Courrier marqué comme traité.');
        });
    }

    // ========================================================================
    // 📤 EXPORT : Excel / PDF / CSV
    // ========================================================================

    public function export(Request $request): StreamedResponse
    {
        return $this->execute(function () use ($request) {
            $format = $request->get('format', 'xlsx');
            $filters = json_decode($request->get('filters', '{}'), true);
            
            // Récupérer les données filtrées
            $query = $this->service->query()
                ->with(['service', 'organisation'])
                ->where('etat', Courrier::ETAT_ACTIF);
            
            // Appliquer les filtres
            if (!empty($filters['type'])) $query->where('type', $filters['type']);
            if (!empty($filters['statut'])) $query->where('statut', $filters['statut']);
            if (!empty($filters['priorite'])) $query->where('priorite', $filters['priorite']);
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('reference', 'like', "%{$filters['search']}%")
                      ->orWhere('objet', 'like', "%{$filters['search']}%")
                      ->orWhere('expediteur', 'like', "%{$filters['search']}%");
                });
            }
            
            $courriers = $query->orderBy('date_reception', 'desc')->get();
            
            return match($format) {
                'pdf' => $this->exportPDF($courriers),
                'csv' => $this->exportCSV($courriers),
                default => $this->exportExcel($courriers),
            };
        });
    }

    // ── Helpers d'export ──
    
    protected function exportExcel($courriers): StreamedResponse
    {
        return response()->streamDownload(function() use ($courriers) {
            $output = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($output, [
                'Référence', 'Objet', 'Type', 'Priorité', 'Statut',
                'Expéditeur', 'Destinataire', 'Service', 'Date réception', 'Date traitement'
            ], ';');
            
            // Données
            foreach ($courriers as $c) {
                fputcsv($output, [
                    $c->reference ?? '—',
                    $c->objet ?? '—',
                    $this->getLabel('type', $c->type),
                    $this->getLabel('priorite', $c->priorite),
                    $this->getLabel('statut', $c->statut),
                    $c->expediteur ?? '—',
                    $c->destinataire ?? '—',
                    $c->service?->nom ?? '—',
                    $c->date_reception ?? '—',
                    $c->date_traitement ?? '—',
                ], ';');
            }
            fclose($output);
        }, 'courriers_export_'.date('Y-m-d_H-i').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function exportCSV($courriers): StreamedResponse
    {
        return $this->exportExcel($courriers); // Même logique, extension différente
    }

    protected function exportPDF($courriers): StreamedResponse
    {
        // Si tu utilises dompdf ou snappy
        // $pdf = \PDF::loadView('courriers.export-pdf', compact('courriers'));
        // return $pdf->download('courriers_'.date('Y-m-d').'.pdf');
        
        // Fallback : rediriger vers Excel si PDF non configuré
        return $this->exportExcel($courriers);
    }

    protected function getLabel(string $field, ?int $value): string
    {
        $maps = [
            'type' => [1 => 'Entrant', 2 => 'Sortant', 3 => 'Interne'],
            'priorite' => [1 => 'Normale', 2 => 'Urgente', 3 => 'Très urgente'],
            'statut' => [1 => 'Enregistré', 2 => 'Affecté', 3 => 'Archivé'],
        ];
        return $maps[$field][$value] ?? '—';
    }

    // ========================================================================
    // 📊 STATS : Dashboard (JSON avec cache)
    // ========================================================================

    public function stats(): JsonResponse
    {
        return $this->execute(function () {
            $stats = Cache::remember('courriers_stats', 300, fn() => $this->service->getStats());
            return $this->respondSuccess('Statistiques chargées.', $stats);
        });
    }

    // ========================================================================
    // 🗂️ UPLOAD TEST : Endpoint pour tester l'upload (dev only)
    // ========================================================================

    public function testUpload(Request $request): JsonResponse
    {
        if (!app()->environment('local')) {
            return $this->respondError('Endpoint réservé au développement.', [], 403);
        }

        $request->validate(['fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240']);
        
        $path = $request->file('fichier')->store('test-uploads', 'public');
        
        return $this->respondSuccess('Fichier uploadé avec succès.', [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}