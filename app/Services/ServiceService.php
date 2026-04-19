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
            ->with(['organisation'])
            ->withCount('agents') // Compte les agents sans requête N+1
            ->latest('nom');

        // Filtres optionnels
        !empty($filtres['nom']) && $query->where('nom', 'LIKE', "%{$filtres['nom']}%");
        !empty($filtres['organisation_id']) && $query->where('organisation_id', $filtres['organisation_id']);

        return $query->get()->map(fn($s) => $this->formatService($s))->toArray();
    }

    /**
     * Formate un service avec ses métadonnées et relations.
     */
    public function formatService(Service $service): array
    {
        $service->loadMissing(['organisation']);

        return [
            'id'          => $service->id,
            'nom'         => $service->nom,
            'organisation' => $service->organisation ? [
                'id'    => $service->organisation->id,
                'nom'   => $service->organisation->nom,
                'sigle' => $service->organisation->sigle,
            ] : null,
            'agents_lies' => $service->agents_count ?? 0,
            'etat'        => $service->etat === Service::ETAT_ACTIF ? 'actif' : 'inactif',
            'created_at'  => $service->created_at?->format('Y-m-d H:i:s'),
            'updated_at'  => $service->updated_at?->format('Y-m-d H:i:s'),
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
     * Retourne les services actifs d'une organisation donnée, formatés.
     */
    public function getByOrganisation(int $organisationId): array
    {
        return $this->repo->getByOrganisation($organisationId)
            ->map(fn($s) => $this->formatService($s))
            ->toArray();
    }

    /**
     * Retourne les statistiques pour le tableau de bord.
     */
    public function getStats(): array
    {
        return [
            'total_actifs'       => $this->repo->query()->where('etat', Service::ETAT_ACTIF)->count(),
            'total_inactifs'     => $this->repo->query()->where('etat', Service::ETAT_INACTIF)->count(),
            'par_organisation'   => $this->repo->countByOrganisation(),
            'agents_par_service' => $this->repo->countAgentsByService(),
        ];
    }
}