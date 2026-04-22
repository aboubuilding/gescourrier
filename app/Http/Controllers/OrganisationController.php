<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganisationFormRequest;
use App\Http\Requests\UpdateOrganisationRequest;
use App\Services\OrganisationService;
use App\Models\Organisation;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * OrganisationController
 * Gestion complète des organisations : vues Blade + API AJAX + exports + modals
 */
class OrganisationController extends BaseController
{
    protected OrganisationService $service;
    
    // Types d'organisation
    const TYPE_EXTERNE = 0;
    const TYPE_INTERNE = 1;
    const TYPE_GOUVERNEMENTALE = 2;
    const TYPE_PRIVEE = 3;
    const TYPE_ONG = 4;
    
    // États
    const ETAT_ACTIF = 1;
    const ETAT_INACTIF = 0;
    
    // Messages d'erreur
    const ERROR_NOT_FOUND = "L'organisation demandée n'existe pas.";
    const ERROR_DELETE = "Impossible de supprimer l'organisation car elle possède des courriers associés.";
    const ERROR_UNIQUE = "Un organisation avec ce nom ou sigle existe déjà.";
    const ERROR_SERVER = "Une erreur inattendue s'est produite. Veuillez réessayer.";

    public function __construct(OrganisationService $service)
    {
        $this->service = $service;
        
        // Middleware d'autorisation
        $this->middleware('role:admin,super_admin')->only([
            'store', 'update', 'destroy', 'restaurer', 'disable', 'enable'
        ]);
    }

    // ========================================================================
    // 📄 INDEX : Vue Blade + Données pour DataTables
    // ========================================================================

    public function index(Request $request): View
    {
        try {
            $filtres = $request->only(['type', 'status', 'search']);
            
            // Données pour la vue
            $organisations = $this->service->liste($filtres);
            
            // Stats pour les KPI (cache 5 min)
            $stats = Cache::remember('organisations_stats', 300, function() {
                return $this->service->getStats();
            });
            
            return view('organisations.index', compact('organisations', 'filtres', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Erreur index organisations: ' . $e->getMessage());
            return view('organisations.index', [
                'organisations' => collect([]),
                'filtres' => [],
                'stats' => $this->getEmptyStats(),
                'error' => self::ERROR_SERVER
            ]);
        }
    }

    // ========================================================================
    // 📡 API : Données JSON pour DataTables (Server-side processing)
    // ========================================================================

    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $query = $this->service->query()
                ->withCount('courriers')
                ->orderBy('nom');

            // Recherche globale
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('sigle', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('adresse', 'like', "%{$search}%");
                });
            }

            // Filtres
            if ($request->filled('type_filter')) {
                $query->where('type', $request->input('type_filter'));
            }
            
            if ($request->filled('status_filter')) {
                $status = $request->input('status_filter');
                $query->where('is_active', $status === 'active' ? 1 : 0);
            }

            // Tri
            if ($request->filled('order_by')) {
                $orderBy = $request->input('order_by');
                $direction = $request->input('direction', 'asc');
                $query->orderBy($orderBy, $direction);
            }

            // Pagination
            $perPage = $request->input('per_page', 15);
            $organisations = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $organisations->items(),
                'current_page' => $organisations->currentPage(),
                'last_page' => $organisations->lastPage(),
                'per_page' => $organisations->perPage(),
                'total' => $organisations->total()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur apiIndex organisations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================================================================
    // 📥 STORE : Création (AJAX + Validation)
    // ========================================================================

    public function store(OrganisationFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            $organisation = $this->service->creer($validated);
            
            DB::commit();
            
            // Invalider le cache
            Cache::forget('organisations_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation créée avec succès.',
                'data' => $this->formatOrganisationForResponse($organisation)
            ], 201);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Erreur création organisation (DB): ' . $e->getMessage());
            
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => self::ERROR_UNIQUE,
                    'errors' => ['nom' => [self::ERROR_UNIQUE]]
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'organisation.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création organisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================================================================
    // ✏️ EDIT : Pré-remplissage du modal modification (JSON)
    // ========================================================================

    public function edit(int $id): JsonResponse
    {
        try {
            $organisation = Organisation::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Données récupérées avec succès.',
                'data' => [
                    'id' => $organisation->id,
                    'nom' => $organisation->nom,
                    'sigle' => $organisation->sigle,
                    'type' => $organisation->type,
                    'adresse' => $organisation->adresse,
                    'telephone' => $organisation->telephone,
                    'email' => $organisation->email,
                    'is_active' => $organisation->is_active,
                    'created_at' => $organisation->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $organisation->updated_at?->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Organisation non trouvée pour édition: ' . $id);
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur édition organisation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================================================================
    // ✏️ UPDATE : Modification (AJAX)
    // ========================================================================

    public function update(UpdateOrganisationRequest $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $organisation = Organisation::findOrFail($id);
            $validated = $request->validated();
            $organisation->update($validated);
            
            DB::commit();
            
            Cache::forget('organisations_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation mise à jour avec succès.',
                'data' => $this->formatOrganisationForResponse($organisation)
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Erreur update organisation (DB): ' . $e->getMessage());
            
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => self::ERROR_UNIQUE,
                    'errors' => ['sigle' => [self::ERROR_UNIQUE]]
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur update organisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================================================================
    // 👁️ SHOW : Lecture détaillée avec courriers associés (JSON)
    // ========================================================================

    public function show(int $id): JsonResponse
    {
        try {
            $organisation = Organisation::with(['courriers' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(50);
            }])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Détails de l\'organisation récupérés.',
                'data' => [
                    'id' => $organisation->id,
                    'nom' => $organisation->nom,
                    'sigle' => $organisation->sigle,
                    'type' => $organisation->type,
                    'type_label' => $this->getTypeLabel($organisation->type),
                    'adresse' => $organisation->adresse,
                    'telephone' => $organisation->telephone,
                    'email' => $organisation->email,
                    
                    'courriers_count' => $organisation->courriers->count(),
                    'courriers' => $organisation->courriers->map(function($c) {
                        return [
                            'id' => $c->id,
                            'reference' => $c->reference,
                            'objet' => $c->objet,
                            'type' => $c->type,
                            'priorite' => $c->priorite,
                            'date_reception' => $c->date_reception,
                            'date_envoi' => $c->date_envoi,
                            'created_at' => $c->created_at?->format('d/m/Y')
                        ];
                    }),
                    'created_at' => $organisation->created_at?->format('d/m/Y'),
                    'created_at_full' => $organisation->created_at?->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur show organisation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================================================================
    // 🗑️ DESTROY : Suppression (avec vérification des dépendances)
    // ========================================================================

    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $organisation = Organisation::findOrFail($id);
            
            // Vérifier si l'organisation a des courriers
            if ($organisation->courriers()->count() > 0) {
                throw new \Exception(self::ERROR_DELETE);
            }
            
            $organisation->delete();
            
            DB::commit();
            
            Cache::forget('organisations_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation supprimée définitivement.'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression organisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() === self::ERROR_DELETE 
                    ? self::ERROR_DELETE 
                    : self::ERROR_SERVER
            ], $e->getMessage() === self::ERROR_DELETE ? 409 : 500);
        }
    }

    // ========================================================================
    // 🔒 DISABLE : Désactiver une organisation
    // ========================================================================

    public function disable(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $organisation = Organisation::findOrFail($id);
            $organisation->update(['is_active' => false]);
            
            DB::commit();
            
            Cache::forget('organisations_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation désactivée avec succès.',
                'data' => ['is_active' => false]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur désactivation organisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER
            ], 500);
        }
    }

    // ========================================================================
    // 🔓 ENABLE : Réactiver une organisation
    // ========================================================================

    public function enable(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $organisation = Organisation::findOrFail($id);
            $organisation->update(['is_active' => true]);
            
            DB::commit();
            
            Cache::forget('organisations_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation réactivée avec succès.',
                'data' => ['is_active' => true]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur réactivation organisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER
            ], 500);
        }
    }

    // ========================================================================
    // ♻️ RESTAURER : Restaurer une organisation supprimée (soft delete)
    // ========================================================================

    public function restaurer(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $organisation = Organisation::withTrashed()->findOrFail($id);
            $organisation->restore();
            
            DB::commit();
            
            Cache::forget('organisations_stats');
            
            return response()->json([
                'success' => true,
                'message' => 'Organisation restaurée avec succès.'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => self::ERROR_NOT_FOUND
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur restauration organisation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER
            ], 500);
        }
    }

    // ========================================================================
    // 📤 EXPORT : Excel / CSV
    // ========================================================================

    public function export(Request $request): StreamedResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $filters = json_decode($request->get('filters', '{}'), true);
            
            // Récupérer les données filtrées
            $query = Organisation::query();
            
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('sigle', 'like', "%{$search}%");
                });
            }
            
            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }
            
            if (!empty($filters['status'])) {
                $query->where('is_active', $filters['status'] === 'active');
            }
            
            $organisations = $query->orderBy('nom')->get();
            
            return $this->exportToCSV($organisations);
            
        } catch (\Exception $e) {
            Log::error('Erreur export organisations: ' . $e->getMessage());
            
            return response()->streamDownload(function() {
                echo "Erreur lors de l'exportation des données";
            }, 'error.csv', ['Content-Type' => 'text/csv']);
        }
    }

    protected function exportToCSV($organisations): StreamedResponse
    {
        return response()->streamDownload(function() use ($organisations) {
            $handle = fopen('php://output', 'w');
            
            // En-têtes UTF-8 avec BOM pour Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($handle, [
                'Nom', 'Sigle', 'Type', 'Email', 'Téléphone', 
                'Adresse', 'Statut', 'Nombre courriers', 'Date création'
            ], ';');
            
            // Données
            foreach ($organisations as $org) {
                fputcsv($handle, [
                    $org->nom ?? '—',
                    $org->sigle ?? '—',
                    $this->getTypeLabel($org->type),
                    $org->email ?? '—',
                    $org->telephone ?? '—',
                    $org->adresse ?? '—',
                    $org->is_active ? 'Actif' : 'Inactif',
                    $org->courriers()->count(),
                    $org->created_at?->format('d/m/Y H:i') ?? '—',
                ], ';');
            }
            
            fclose($handle);
        }, 'organisations_' . date('Y-m-d_H-i') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ========================================================================
    // 📊 STATS : Dashboard (JSON avec cache)
    // ========================================================================

    public function stats(): JsonResponse
    {
        try {
            $stats = Cache::remember('organisations_stats', 300, function() {
                return [
                    'total' => Organisation::count(),
                    'active' => Organisation::where('is_active', true)->count(),
                    'inactive' => Organisation::where('is_active', false)->count(),
                    'by_type' => [
                        'externe' => Organisation::where('type', self::TYPE_EXTERNE)->count(),
                        'interne' => Organisation::where('type', self::TYPE_INTERNE)->count(),
                        'gouvernementale' => Organisation::where('type', self::TYPE_GOUVERNEMENTALE)->count(),
                        'privee' => Organisation::where('type', self::TYPE_PRIVEE)->count(),
                        'ong' => Organisation::where('type', self::TYPE_ONG)->count(),
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur stats organisations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => self::ERROR_SERVER,
                'data' => $this->getEmptyStats()
            ], 500);
        }
    }

    // ========================================================================
    // 🔍 RECHERCHE RAPIDE (Autocomplete)
    // ========================================================================

    public function search(Request $request): JsonResponse
    {
        try {
            $term = $request->get('q', '');
            
            if (strlen($term) < 2) {
                return response()->json(['results' => []]);
            }
            
            $organisations = Organisation::where('is_active', true)
                ->where(function($query) use ($term) {
                    $query->where('nom', 'like', "%{$term}%")
                          ->orWhere('sigle', 'like', "%{$term}%");
                })
                ->limit(10)
                ->get();
            
            return response()->json([
                'results' => $organisations->map(function($org) {
                    return [
                        'id' => $org->id,
                        'text' => $org->nom . ($org->sigle ? " ({$org->sigle})" : '')
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur search organisations: ' . $e->getMessage());
            return response()->json(['results' => []], 500);
        }
    }

    // ========================================================================
    // 🗂️ UTILITAIRES PRIVÉS
    // ========================================================================

    protected function formatOrganisationForResponse(Organisation $organisation): array
    {
        return [
            'id' => $organisation->id,
            'nom' => $organisation->nom,
            'sigle' => $organisation->sigle,
            'type' => $organisation->type,
            'type_label' => $this->getTypeLabel($organisation->type),
            'adresse' => $organisation->adresse,
            'telephone' => $organisation->telephone,
            'email' => $organisation->email,
            
            'courriers_count' => $organisation->courriers()->count(),
            'created_at' => $organisation->created_at?->format('d/m/Y H:i')
        ];
    }

    protected function getTypeLabel(?int $type): string
    {
        return match($type) {
            self::TYPE_EXTERNE => 'Externe',
            self::TYPE_INTERNE => 'Interne',
            self::TYPE_GOUVERNEMENTALE => 'Gouvernementale',
            self::TYPE_PRIVEE => 'Privée',
            self::TYPE_ONG => 'ONG',
            default => 'Non défini',
        };
    }

    protected function getEmptyStats(): array
    {
        return [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'by_type' => [
                'externe' => 0,
                'interne' => 0,
                'gouvernementale' => 0,
                'privee' => 0,
                'ong' => 0,
            ]
        ];
    }
}