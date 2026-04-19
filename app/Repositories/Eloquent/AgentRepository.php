<?php

namespace App\Repositories\Eloquent;

use App\Models\Agent;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\AgentInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AgentRepository extends BaseRepository implements AgentInterface
{
   public function __construct(Agent $model)
    {
        parent::__construct($model);
    }

    /**
     * Retourne l'instance typée du modèle (pour l'autocomplétion)
     */
    public function getModel(): Agent
    {
        return $this->model instanceof Agent ? $this->model : throw new \RuntimeException('Modèle incorrect');
    }
    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->query()
            ->when($filtres['nom'] ?? null, fn($q, $v) => $q->where('nom', 'LIKE', "%{$v}%"))
            ->when($filtres['prenom'] ?? null, fn($q, $v) => $q->where('prenom', 'LIKE', "%{$v}%"))
            ->when($filtres['fonction'] ?? null, fn($q, $v) => $q->where('fonction', 'LIKE', "%{$v}%"))
            ->when($filtres['service_id'] ?? null, fn($q, $v) => $q->where('service_id', $v))
            ->when($filtres['etat'] ?? null, fn($q, $v) => $q->where('etat', $v))
            ->latest()
            ->paginate(15);
    }

    public function getByService(int $serviceId): Collection
    {
        return $this->query()->where('service_id', $serviceId)->get();
    }

    public function lierAUser(int $agentId, int $userId): bool
    {
        return $this->update($agentId, ['user_id' => $userId]);
    }

    public function reassignerService(int $agentId, int $serviceId): bool
    {
        return $this->update($agentId, ['service_id' => $serviceId]);
    }

    public function countByService(): array
    {
        return $this->query()
            ->selectRaw('service_id, count(*) as total')
            ->groupBy('service_id')
            ->pluck('total', 'service_id')
            ->toArray();
    }

    public function countActifs(): int
    {
        return $this->query()->where('etat', 1)->count();
    }
}