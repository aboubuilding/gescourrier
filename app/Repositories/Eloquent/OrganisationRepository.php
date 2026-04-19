<?php

namespace App\Repositories\Eloquent;

use App\Models\Organisation;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\OrganisationInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrganisationRepository extends BaseRepository implements OrganisationInterface
{
   public function __construct(Organisation $model)
    {
        parent::__construct($model);
    }

    /**
     * Retourne l'instance typée du modèle (pour l'autocomplétion)
     */
    public function getModel(): Organisation
    {
        return $this->model instanceof Organisation ? $this->model : throw new \RuntimeException('Modèle incorrect');
    }

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->query()
            ->when($filtres['nom'] ?? null, fn($q, $v) => $q->where('nom', 'LIKE', "%{$v}%"))
            ->when($filtres['sigle'] ?? null, fn($q, $v) => $q->where('sigle', 'LIKE', "%{$v}%"))
            ->when($filtres['type'] ?? null, fn($q, $v) => $q->where('type', $v))
            ->when($filtres['etat'] ?? null, fn($q, $v) => $q->where('etat', $v))
            ->latest()
            ->paginate(15);
    }

    public function getByType(int $type): Collection
    {
        return $this->query()->where('type', $type)->get();
    }

    public function findWithServices(int $id): ?Organisation
    {
        return $this->query()->with('services')->find($id);
    }

    public function countByType(): array
    {
        return $this->query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
    }

    public function countTotalServices(): int
    {
        // Sous-requête ou relation comptée pour éviter N+1
        return $this->query()->withCount('services')->get()->sum('services_count');
    }

    public function sigleExiste(string $sigle, ?int $ignoreId = null): bool
    {
        $query = $this->query()->where('sigle', $sigle);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}