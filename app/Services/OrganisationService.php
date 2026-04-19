<?php

namespace App\Services;

use App\Repositories\Interfaces\OrganisationInterface;
use App\Models\Organisation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrganisationService
{
    public function __construct(
        protected OrganisationInterface $repo
    ) {}

    // ========================================================================
    // 📋 LISTE & FORMATAGE
    // ========================================================================

    /**
     * Retourne toutes les organisations actives, formatées et prêtes pour l'API/Frontend.
     */
    public function liste(array $filtres = []): array
    {
        $query = $this->repo->query()
            ->where('etat', Organisation::ETAT_ACTIF)
            ->withCount('services') // Charge le nombre de services sans requête N+1
            ->latest();

        // Filtres optionnels
        !empty($filtres['nom']) && $query->where('nom', 'LIKE', "%{$filtres['nom']}%");
        !empty($filtres['sigle']) && $query->where('sigle', 'LIKE', "%{$filtres['sigle']}%");
        !empty($filtres['type']) && $query->where('type', $filtres['type']);

        return $query->get()->map(fn($o) => $this->formatOrganisation($o))->toArray();
    }

    /**
     * Formate une organisation avec ses métadonnées et libellés métier.
     */
    public function formatOrganisation(Organisation $org): array
    {
        return [
            'id'        => $org->id,
            'nom'       => $org->nom,
            'sigle'     => $org->sigle,
            'type'      => ['code' => $org->type, 'libelle' => $this->getTypeLibelle($org->type)],
            'contact'   => [
                'adresse'   => $org->adresse,
                'telephone' => $org->telephone,
                'email'     => $org->email,
            ],
            'services_lies' => $org->services_count ?? $org->services->count(),
            'etat'      => $org->etat === Organisation::ETAT_ACTIF ? 'actif' : 'inactif',
            'created_at' => $org->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $org->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function getTypeLibelle(int $type): string
    {
        return match($type) {
            0 => 'Externe',
            1 => 'Interne',
            2 => 'Gouvernementale',
            3 => 'Privée',
            4 => 'ONG',
            default => 'Inconnu',
        };
    }

    // ========================================================================
    // ➕ CRUD
    // ========================================================================

    public function creer(array $data): Organisation
    {
        return DB::transaction(function () use ($data) {
            $data['etat'] = Organisation::ETAT_ACTIF;
            return $this->repo->create($data);
        });
    }

    public function mettreAJour(int $id, array $data): Organisation
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
     * Retourne les organisations d'un type spécifique, formatées.
     */
    public function getByType(int $type): array
    {
        return $this->repo->getByType($type)
            ->map(fn($o) => $this->formatOrganisation($o))
            ->toArray();
    }

    /**
     * Retourne une organisation avec la liste de ses services rattachés.
     */
    public function findWithServices(int $id): ?array
    {
        $org = $this->repo->findWithServices($id);
        if (!$org) return null;

        return array_merge($this->formatOrganisation($org), [
            'services' => $org->services->map(fn($s) => [
                'id'   => $s->id,
                'nom'  => $s->nom,
                'etat' => $s->etat === \App\Models\Service::ETAT_ACTIF ? 'actif' : 'inactif',
            ])->toArray()
        ]);
    }

    /**
     * Retourne les statistiques pour le tableau de bord.
     */
    public function getStats(): array
    {
        return [
            'total_actifs'   => $this->repo->query()->where('etat', Organisation::ETAT_ACTIF)->count(),
            'total_inactifs' => $this->repo->query()->where('etat', Organisation::ETAT_INACTIF)->count(),
            'par_type'       => $this->repo->countByType(),
            'total_services' => $this->repo->countTotalServices(),
        ];
    }
}