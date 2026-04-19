<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\ServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository extends BaseRepository implements ServiceInterface
{
   public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    /**
     * Retourne l'instance typée du modèle (pour l'autocomplétion)
     */
    public function getModel(): Service
    {
        return $this->model instanceof Service ? $this->model : throw new \RuntimeException('Modèle incorrect');
    }

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->query()
            ->when($filtres['nom'] ?? null, fn($q, $v) => $q->where('nom', 'LIKE', "%{$v}%"))
            ->when($filtres['organisation_id'] ?? null, fn($q, $v) => $q->where('organisation_id', $v))
            ->when($filtres['etat'] ?? null, fn($q, $v) => $q->where('etat', $v))
            ->latest()
            ->paginate(15);
    }

    public function getByOrganisation(int $organisationId): Collection
    {
        return $this->query()
            ->where('organisation_id', $organisationId)
            ->orderBy('nom')
            ->get();
    }

    public function findWithDetails(int $id): ?Service
    {
        return $this->query()
            ->withCount(['agents' => fn($q) => $q->where('etat', 1)])
            ->find($id);
    }

    public function countByOrganisation(): array
    {
        return $this->query()
            ->selectRaw('organisation_id, count(*) as total')
            ->groupBy('organisation_id')
            ->pluck('total', 'organisation_id')
            ->toArray();
    }

    public function countAgentsByService(): array
    {
        return $this->query()
            ->withCount(['agents' => fn($q) => $q->where('etat', 1)])
            ->get()
            ->mapWithKeys(fn($s) => [$s->id => $s->agents_count])
            ->toArray();
    }

    public function nomExisteDansOrga(string $nom, int $organisationId, ?int $ignoreId = null): bool
    {
        $query = $this->query()
            ->where('organisation_id', $organisationId)
            ->whereRaw('LOWER(nom) = ?', [strtolower($nom)]);
            
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        return $query->exists();
    }
}