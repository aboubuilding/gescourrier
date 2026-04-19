<?php

namespace App\Repositories\Eloquent;

use App\Models\Courrier;
use App\Repositories\Interfaces\CourrierInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CourrierRepository extends BaseRepository implements CourrierInterface
{
    /**
     * Constructeur : injecte le modèle Courrier
     * ⚠️ Le type du paramètre peut être spécifique, mais la propriété héritée reste Model
     */
    public function __construct(Courrier $model)
    {
        parent::__construct($model); // ✅ $model est automatiquement assigné à $this->model (type Model)
    }

    /**
     * Getter typé pour autocomplete et type safety dans ce repository
     * ✅ Retourne Courrier au lieu de Model générique
     */
    public function getModel(): Courrier
    {
        return $this->model instanceof Courrier ? $this->model : throw new \RuntimeException('Modèle incorrect');
    }

    // ========================================================================
    // 📋 Workflow métier (Affectation & Traitement)
    // ========================================================================

    public function affecter(int $courrierId, int $agentId, int $serviceId): bool
    {
        return $this->update($courrierId, [
            'agent_id' => $agentId,
            'service_id' => $serviceId,
            'statut' => Courrier::STATUT_AFFECTE,
            'date_affectation' => now(),
        ]);
    }

    public function marquerTraite(int $courrierId, int $userId): bool
    {
        return $this->update($courrierId, [
            'statut' => Courrier::STATUT_TRAITE,
            'date_traitement' => now(),
            'utilisateur_id' => $userId,
        ]);
    }

    // ========================================================================
    // 🔍 Recherche & Filtres avancés
    // ========================================================================

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->query()
            ->when($filtres['type'] ?? null, fn($q, $v) => $q->where('type', $v))
            ->when($filtres['priorite'] ?? null, fn($q, $v) => $q->where('priorite', $v))
            ->when($filtres['statut'] ?? null, fn($q, $v) => $q->where('statut', $v))
            ->when($filtres['service_id'] ?? null, fn($q, $v) => $q->where('service_id', $v))
            ->when($filtres['organisation_id'] ?? null, fn($q, $v) => $q->where('organisation_id', $v))
            ->when($filtres['date_debut'] ?? null, fn($q, $v) => $q->whereDate('date_reception', '>=', $v))
            ->when($filtres['date_fin'] ?? null, fn($q, $v) => $q->whereDate('date_reception', '<=', $v))
            ->when($filtres['search'] ?? null, fn($q, $v) => 
                $q->where(function($sub) use ($v) {
                    $sub->where('objet', 'like', "%{$v}%")
                        ->orWhere('reference', 'like', "%{$v}%")
                        ->orWhere('expediteur', 'like', "%{$v}%")
                        ->orWhere('destinataire', 'like', "%{$v}%");
                })
            )
            ->latest('date_reception')
            ->paginate(15);
    }

    // ========================================================================
    // 🚨 Méthodes de l'interface CourrierInterface
    // ========================================================================

    public function getUrgentsNonTraites(): Collection
    {
        return $this->query()
            ->where('priorite', Courrier::PRIORITE_TRES_URGENTE)
            ->whereIn('statut', [Courrier::STATUT_ENREGISTRE, Courrier::STATUT_AFFECTE])
            ->orderBy('date_reception', 'desc')
            ->get();
    }

    public function getByService(int $serviceId, ?int $statut = null): Collection
    {
        $query = $this->query()->where('service_id', $serviceId);
        
        if ($statut !== null) {
            $query->where('statut', $statut);
        }
        
        return $query->orderBy('date_reception', 'desc')->get();
    }

    public function attacherFichier(int $courrierId, array $fichierData): bool
    {
        return $this->update($courrierId, $fichierData);
    }

    public function countByStatut(): array
    {
        return $this->query()
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();
    }

    public function countByPeriode(string $periode = 'month'): array
    {
        $format = match($periode) {
            'day' => '%Y-%m-%d',
            'year' => '%Y',
            default => '%Y-%m',
        };

        return $this->query()
            ->selectRaw("DATE_FORMAT(date_reception, '{$format}') as periode, count(*) as total")
            ->groupBy('periode')
            ->orderBy('periode')
            ->pluck('total', 'periode')
            ->toArray();
    }
}