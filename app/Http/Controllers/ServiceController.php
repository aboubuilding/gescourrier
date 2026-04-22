<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceFormRequest;
use App\Http\Requests\UpdateServiceFormRequest;
use App\Services\ServiceService;
use App\Models\Service;
use App\Models\Courrier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ServiceController extends BaseController
{
    public function __construct(protected ServiceService $service)
    {
    }



public function index(Request $request): View
{
    try {
        // =========================================================
        // 1. FILTRES PROPREMENT NORMALISÉS
        // =========================================================
        $filtres = [
            'search' => trim($request->input('search', '')),
            'nom'    => trim($request->input('nom', '')),
            'type'   => $request->input('type', null),
            'etat'   => $request->input('etat', null),
        ];

        // Nettoyage des valeurs vides
        $filtres = array_filter($filtres, fn ($v) => $v !== null && $v !== '');

        // =========================================================
        // 2. LISTE PRINCIPALE
        // =========================================================
        $services = $this->service->liste($filtres);

        // 🔥 Sécurisation des counts (évite ton erreur courante)
        $services->loadCount([
            'agents',
            'courriers'
        ]);

        // =========================================================
        // 3. STATS OPTIMISÉES (CACHE)
        // =========================================================
        $stats = Cache::remember('services_stats_v2', 300, function () {

            return [
                'total'        => \App\Models\Service::count(),
                'actifs'       => \App\Models\Service::where('etat', 'actif')->count(),
                'inactifs'     => \App\Models\Service::where('etat', 'inactif')->count(),

                // si relation agents existe
                'total_agents' => \App\Models\Agent::count(),
            ];
        });

        // =========================================================
        // 4. VARIABLE POUR MODAL (CORRECTION ERREUR "Undefined variable")
        // =========================================================
        $organisations = \App\Models\Organisation::select('id', 'nom')
            ->orderBy('nom')
            ->get();

        // =========================================================
        // 5. VIEW
        // =========================================================
        return view('services.index', [
            'services'       => $services,
            'filtres'        => $filtres,
            'stats'          => $stats,
            'organisations'  => $organisations, // 🔥 FIX ERREUR MODAL
        ]);

    } catch (\Throwable $e) {

        Log::error('Erreur index services', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);

        return view('services.index', [
            'services'      => collect(),
            'filtres'       => [],
            'stats'         => [
                'total'        => 0,
                'actifs'       => 0,
                'inactifs'     => 0,
                'total_agents' => 0,
            ],
            'organisations' => collect(), // 🔥 évite crash modal
            'error'         => 'Erreur lors du chargement des services'
        ]);
    }
}

    // =========================================================
    // 📡 API DATATABLES
    // =========================================================
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            return $this->execute(function () use ($request) {

                $query = Service::query()
                    ->withCount(['agents', 'courriers']);

                if ($request->filled('search.value')) {
                    $search = $request->input('search.value');

                    $query->where(function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                          ->orWhereHas('organisation', fn($o) =>
                              $o->where('nom', 'like', "%{$search}%")
                          );
                    });
                }

                $total = Service::count();
                $filtered = (clone $query)->count();

                $services = $query
                    ->offset($request->input('start', 0))
                    ->limit($request->input('length', 15))
                    ->get();

                return response()->json([
                    'draw' => (int) $request->input('draw'),
                    'recordsTotal' => $total,
                    'recordsFiltered' => $filtered,
                    'data' => $services->map(fn($s) => $this->service->formatService($s)),
                ]);
            });

        } catch (\Throwable $e) {
            Log::error('API services error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Erreur serveur API services'
            ], 500);
        }
    }

    // =========================================================
    // 📥 STORE
    // =========================================================
    public function store(ServiceFormRequest $request): JsonResponse
    {
        try {
            return $this->execute(function () use ($request) {

                $service = $this->service->creer($request->validated());

                Cache::forget('services_stats');

                return $this->respondSuccess(
                    'Service créé avec succès.',
                    $this->service->formatService($service),
                    201
                );
            });

        } catch (\Throwable $e) {
            Log::error('Store service error: ' . $e->getMessage());

            return $this->respondError('Erreur lors de la création du service');
        }
    }

    // =========================================================
    // ✏️ EDIT
    // =========================================================
    public function edit(int $id): JsonResponse
    {
        try {
            return $this->execute(function () use ($id) {

                $service = Service::findOrFail($id);

                return $this->respondSuccess('OK', [
                    'id' => $service->id,
                    'nom' => $service->nom,
                ]);
            });

        } catch (\Throwable $e) {
            Log::error('Edit service error: ' . $e->getMessage());

            return $this->respondError('Service introuvable');
        }
    }

    // =========================================================
    // ✏️ UPDATE
    // =========================================================
    public function update(UpdateServiceFormRequest $request, int $id): JsonResponse
    {
        try {
            return $this->execute(function () use ($request, $id) {

                $service = $this->service->mettreAJour($id, $request->validated());

                Cache::forget('services_stats');

                return $this->respondSuccess(
                    'Service mis à jour avec succès.',
                    $this->service->formatService($service)
                );
            });

        } catch (\Throwable $e) {
            Log::error('Update service error: ' . $e->getMessage());

            return $this->respondError('Erreur lors de la mise à jour');
        }
    }

    // =========================================================
    // 👁️ SHOW (🔥 COMPLET + SAFE)
    // =========================================================
    public function show(int $id): JsonResponse
    {
        try {
            return $this->execute(function () use ($id) {

                $service = Service::with([
                        'organisation',
                        'agents' => fn($q) => $q->withCount('courriers')
                    ])
                    ->withCount(['agents', 'courriers'])
                    ->findOrFail($id);

                return $this->respondSuccess('Service récupéré.', [
                    'id' => $service->id,
                    'nom' => $service->nom,

                    'total_agents' => $service->agents_count,
                    'total_courriers' => $service->courriers_count,

                    'agents' => $service->agents->map(fn($a) => [
                        'id' => $a->id,
                        'nom' => $a->nom,
                        'courriers_affectes' => $a->courriers_count,
                    ]),

                    'top_agent' => $service->agents
                        ->sortByDesc('courriers_count')
                        ->first(),

                    'agents_sans_courrier' => $service->agents
                        ->where('courriers_count', 0)
                        ->values()
                        ->map(fn($a) => [
                            'id' => $a->id,
                            'nom' => $a->nom,
                        ]),

                    'courriers_par_statut' => Courrier::where('service_id', $service->id)
                        ->selectRaw('statut, COUNT(*) as total')
                        ->groupBy('statut')
                        ->pluck('total', 'statut'),

                    'created_at' => $service->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $service->updated_at?->format('Y-m-d H:i:s'),
                ]);
            });

        } catch (\Throwable $e) {
            Log::error('Show service error: ' . $e->getMessage());

            return $this->respondError('Service introuvable ou erreur serveur');
        }
    }

    // =========================================================
    // 🗑️ DELETE
    // =========================================================
    public function destroy(int $id): JsonResponse
    {
        try {
            return $this->execute(function () use ($id) {

                $this->service->supprimer($id);

                Cache::forget('services_stats');

                return $this->respondSuccess('Service supprimé.');
            });

        } catch (\Throwable $e) {
            Log::error('Delete service error: ' . $e->getMessage());

            return $this->respondError('Erreur lors de la suppression');
        }
    }

    // =========================================================
    // 📊 STATS
    // =========================================================
    public function stats(): JsonResponse
    {
        try {
            return $this->execute(function () {

                $stats = Cache::remember(
                    'services_stats',
                    300,
                    fn() => $this->service->getStats()
                );

                return $this->respondSuccess('OK', $stats);
            });

        } catch (\Throwable $e) {
            Log::error('Stats error: ' . $e->getMessage());

            return $this->respondError('Erreur lors du chargement des stats');
        }
    }
}