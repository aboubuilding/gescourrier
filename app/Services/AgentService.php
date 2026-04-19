<?php

namespace App\Services;

use App\Repositories\Interfaces\AgentInterface;
use App\Models\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AgentService
{
    public function __construct(
        protected AgentInterface $repo
    ) {}

    // ========================================================================
    // 📋 LISTE & FORMATAGE
    // ========================================================================

    /**
     * Retourne tous les agents actifs, formatés et prêts pour l'API/Frontend.
     * @param array $filtres Optionnels : service_id, fonction, nom
     */
    public function liste(array $filtres = []): array
    {
        $query = $this->repo->query()
            ->where('etat', Agent::ETAT_ACTIF)
            ->with(['user', 'service']) // Évite les requêtes N+1
            ->latest();

        // Filtres optionnels
        !empty($filtres['service_id']) && $query->where('service_id', $filtres['service_id']);
        !empty($filtres['fonction']) && $query->where('fonction', 'LIKE', "%{$filtres['fonction']}%");
        !empty($filtres['nom']) && $query->where(function($q) use ($filtres) {
            $q->where('nom', 'LIKE', "%{$filtres['nom']}%")
              ->orWhere('prenom', 'LIKE', "%{$filtres['nom']}%");
        });

        return $query->get()->map(fn($a) => $this->formatAgent($a))->toArray();
    }

    /**
     * Formate un agent avec ses relations, métadonnées et libellés métier.
     */
    public function formatAgent(Agent $agent): array
    {
        $agent->loadMissing(['user', 'service']);

        return [
            // 🪪 Identité
            'id'           => $agent->id,
            'nom'          => $agent->nom,
            'prenom'       => $agent->prenom,
            'nom_complet'  => trim("{$agent->nom} {$agent->prenom}"),
            'email'        => $agent->email,
            'telephone'    => $agent->telephone,
            'fonction'     => $agent->fonction,

            // 🔐 Compte utilisateur lié
            'user' => $agent->user ? [
                'id'                 => $agent->user->id,
                'email'              => $agent->user->email,
                'role'               => $agent->user->role,
                'derniere_connexion' => $agent->user->derniere_connexion?->format('Y-m-d H:i'),
                'actif'              => $agent->user->etat === \App\Models\User::ETAT_ACTIF,
            ] : null,

            // 🏢 Rattachement métier
            'service' => $agent->service ? [
                'id'             => $agent->service->id,
                'nom'            => $agent->service->nom,
                'organisation'   => $agent->service->organisation?->nom ?? null,
                'sigle_orga'     => $agent->service->organisation?->sigle ?? null,
            ] : null,

            // ⏱️ Horodatage & État
            'etat'       => $agent->etat === Agent::ETAT_ACTIF ? 'actif' : 'inactif',
            'created_at' => $agent->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $agent->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    // ========================================================================
    // ➕ Création & Mise à jour
    // ========================================================================

    public function creer(array $data): Agent
    {
        return DB::transaction(function () use ($data) {
            $data['etat'] = Agent::ETAT_ACTIF;
            return $this->repo->create($data);
        });
    }

    public function mettreAJour(int $id, array $data): Agent
    {
        $this->repo->update($id, $data);
        return $this->repo->findOrFail($id);
    }

    // ========================================================================
    // 🗑️ Gestion du cycle de vie (etat)
    // ========================================================================

    public function supprimer(int $id): bool { return $this->repo->supprimer($id); }
    public function restaurer(int $id): bool   { return $this->repo->restaurer($id); }

    // ========================================================================
    // 🔗 Workflow métier
    // ========================================================================

    public function lierAUser(int $agentId, int $userId): bool
    {
        return $this->repo->lierAUser($agentId, $userId);
    }

    public function reassignerService(int $agentId, int $serviceId): bool
    {
        return $this->repo->reassignerService($agentId, $serviceId);
    }

    // ========================================================================
    // 🔍 Recherche & Dashboard
    // ========================================================================

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->repo->rechercher($filtres);
    }

    public function getStats(): array
    {
        return [
            'total_actifs' => $this->repo->countActifs(),
            'par_service'  => $this->repo->countByService(),
        ];
    }
}