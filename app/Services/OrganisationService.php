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
        ->withCount('courriers') // Charge le nombre de courriers liés à l'organisation
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc');

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
        'statistiques' => [
            'courriers' => [
                'total' => $org->courriers_count ?? 0, // Correction ici
                'entrants' => $org->courriers_entrants_count ?? 0,
                'sortants' => $org->courriers_sortants_count ?? 0,
                'internes' => $org->courriers_internes_count ?? 0,
                'urgents' => $org->courriers_urgents_count ?? 0,
            ],
            
        ],
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

public function supprimer(int $id): bool
{
    $organisation = $this->repo->find($id);
    
    if (!$organisation) {
        return false;
    }
    
    // Changer l'état à inactif (0) au lieu de supprimer définitivement
    return $organisation->update(['etat' => Organisation::ETAT_INACTIF]); // 0 = inactif
}
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
    $query = $this->repo->query()->where('etat', Organisation::ETAT_ACTIF);
    
    return [
        'total_organisations' => $query->count(),
        'par_type' => [
            'Externe' => $query->clone()->where('type', 0)->count(),
            'Interne' => $query->clone()->where('type', 1)->count(),
            'Gouvernementale' => $query->clone()->where('type', 2)->count(),
            'Privée' => $query->clone()->where('type', 3)->count(),
            'ONG' => $query->clone()->where('type', 4)->count(),
        ],
    ];
}
}