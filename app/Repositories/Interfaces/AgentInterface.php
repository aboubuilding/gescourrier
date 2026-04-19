<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * AgentInterface
 * Contrat pour les opérations métier spécifiques au module "Agent".
 * Le CRUD de base (find, create, update, supprimer, restaurer) est géré par BaseRepositoryInterface.
 */
interface AgentInterface
{
    // ========================================================================
    // 🔍 Recherche & Filtres avancés
    // ========================================================================

    /**
     * Recherche paginée avec filtres dynamiques.
     * @param array{
     *   nom?: string,
     *   prenom?: string,
     *   fonction?: string,
     *   service_id?: int,
     *   etat?: int
     * } $filtres
     */
    public function rechercher(array $filtres): LengthAwarePaginator;

    /**
     * Récupère tous les agents actifs d'un service donné.
     */
    public function getByService(int $serviceId): Collection;

    // ========================================================================
    // 🔗 Synchronisation & Gestion des relations
    // ========================================================================

    /**
     * Associe un compte utilisateur Laravel à un agent (pour l'authentification).
     * @return bool
     */
    public function lierAUser(int $agentId, int $userId): bool;

    /**
     * Change le service de rattachement d'un agent.
     * @return bool
     */
    public function reassignerService(int $agentId, int $serviceId): bool;

    // ========================================================================
    // 📊 Agrégations / Dashboard
    // ========================================================================

    /**
     * Retourne le nombre d'agents groupés par service.
     * @return array<int, int> ['service_id' => count]
     */
    public function countByService(): array;

    /**
     * Retourne le nombre total d'agents actifs (etat = 1).
     */
    public function countActifs(): int;
}