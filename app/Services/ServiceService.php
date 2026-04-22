<?php

namespace App\Services;

use App\Repositories\Interfaces\ServiceInterface;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ServiceService
{
    public function __construct(
        protected ServiceInterface $repo
    ) {}

    // ========================================================================
    // 📋 LISTE & FORMATAGE
    // ========================================================================

    /**
     * Retourne tous les services actifs, formatés et prêts pour l'API/Frontend.
     * @param array $filtres Optionnels : nom, organisation_id
     */
    public function liste(array $filtres = []): array
{
    $query = $this->repo->query()
        ->where('etat', Service::ETAT_ACTIF)
       ->withCount([
        'agents',
        'courriers'
    ])
        ->orderByDesc('created_at')
        ->orderByDesc('id');

    // Filtre sur le nom uniquement
    if (isset($filtres['nom']) && trim($filtres['nom']) !== '') {
        $nom = trim($filtres['nom']);
        $query->where('nom', 'LIKE', "%{$nom}%");
    }

    return $query->get()
        ->map(fn($s) => $this->formatService($s))
        ->toArray();
}

    /**
     * Formate un service avec ses métadonnées et relations.
     */
    public function formatService(Service $service): array
{
    return [
        'id' => (int) $service->id,
        'nom' => (string) $service->nom,

        'agents_lies' => (int) $service->agents_count,
        'courriers_lies' => (int) $service->courriers_count,

        'created_at' => $service->created_at?->format('Y-m-d H:i:s'),
        'updated_at' => $service->updated_at?->format('Y-m-d H:i:s'),
    ];
}
    // ========================================================================
    // ➕ CRUD
    // ========================================================================

    public function creer(array $data): Service
    {
        return DB::transaction(function () use ($data) {
            $data['etat'] = Service::ETAT_ACTIF;
            return $this->repo->create($data);
        });
    }

    public function mettreAJour(int $id, array $data): Service
    {
        $this->repo->update($id, $data);
        return $this->repo->findOrFail($id);
    }

    public function supprimer(int $id): bool { return $this->repo->supprimer($id); }
    public function restaurer(int $id): bool   { return $this->repo->restaurer($id); }

    // ========================================================================
    // 🔍 RECHERCHE & MÉTIER
    // ========================================================================

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->repo->rechercher($filtres);
    }

    

    /**
     * Retourne les statistiques pour le tableau de bord.
     */
    public function getStats(): array
{
    $baseQuery = $this->repo->query()->withCount(['courriers', 'agents']);

    // 🔢 Totaux
    $totalActifs = (clone $this->repo->query())->count();

    $totalInactifs = (clone $this->repo->query())
        ->withoutGlobalScopes()
        ->where('etat', Service::ETAT_INACTIF)
        ->count();

    // 📬 Services sans courrier
    $servicesSansCourrier = (clone $baseQuery)
        ->having('courriers_count', '=', 0)
        ->count();

    // 👨‍💼 Services sans agents
    $servicesSansAgents = (clone $baseQuery)
        ->having('agents_count', '=', 0)
        ->count();

    // 🏆 Service avec le plus de courriers
    $topService = (clone $baseQuery)
        ->orderByDesc('courriers_count')
        ->first();

    // 📊 Total courriers (via relation)
    $totalCourriers = \App\Models\Courrier::count();

    // 📈 Moyenne de courriers par service
    $moyenneCourriers = $totalActifs > 0
        ? round($totalCourriers / $totalActifs, 2)
        : 0;

    return [
        'total_actifs' => $totalActifs,
        'total_inactifs' => $totalInactifs,

        'services_sans_courrier' => $servicesSansCourrier,
        'services_sans_agents' => $servicesSansAgents,

        'total_courriers' => $totalCourriers,
        'moyenne_courriers_par_service' => $moyenneCourriers,

        'top_service' => $topService ? [
            'id' => $topService->id,
            'nom' => $topService->nom,
            'courriers' => $topService->courriers_count,
        ] : null,

        'par_organisation' => $this->repo->countByOrganisation(),
        'agents_par_service' => $this->repo->countAgentsByService(),
    ];
}
}