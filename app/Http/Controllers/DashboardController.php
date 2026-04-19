<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * DashboardController
 * Point d'entrée du tableau de bord.
 * Sépare le rendu de la vue (Blade) des appels de données (JSON/AJAX).
 */
class DashboardController extends BaseController
{
    public function __construct(protected DashboardService $dashboardService) {}

    // ========================================================================
    // 📄 INDEX : Retourne la Vue Blade (structure du dashboard)
    // ========================================================================

    public function index(Request $request): View
    {
        // La vue charge la structure. Les données sont récupérées via AJAX
        // pour éviter de bloquer le premier rendu et permettre le rafraîchissement dynamique.
        return view('dashboard.index');
    }

    // ========================================================================
    // 📊 OVERVIEW : Données du tableau de bord (JSON)
    // ========================================================================

    public function overview(): JsonResponse
    {
        return $this->execute(function () {
            // Le cache est géré dans DashboardService, mais tu peux le forcer ici si besoin :
            $data = Cache::remember('dashboard_overview', 300, fn() => 
                $this->dashboardService->getOverview()
            );

            return $this->respondSuccess('Données du tableau de bord chargées.', $data);
        });
    }

    // ========================================================================
    // 🔄 REFRESH : Rafraîchissement manuel (optionnel)
    // ========================================================================

    public function refresh(): JsonResponse
    {
        return $this->execute(function () {
            // Force l'invalidation du cache avant de récupérer les données
            Cache::forget('dashboard_overview');
            
            $data = $this->dashboardService->getOverview();
            return $this->respondSuccess('Tableau de bord actualisé.', $data);
        });
    }
}