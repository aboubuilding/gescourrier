<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ServiceInterface
 * Contrat pour les opérations métier spécifiques au module "Service".
 * Le CRUD de base (find, create, update, supprimer, restaurer) est géré par BaseRepositoryInterface.
 */
interface ServiceInterface
{
    // ========================================================================
    // 🔍 Recherche & Filtres
    // ========================================================================

    /**
     * Recherche paginée avec filtres dynamiques.
     * @param array{
     *   nom?: string,
     *   organisation_id?: int,
     *   etat?: int
     * } $filtres
     */
    public function rechercher(array $filtres): LengthAwarePaginator;

    /**
     * Récupère tous les services actifs rattachés à une organisation.
     */
    public function getByOrganisation(int $organisationId): Collection;

    /**
     * Récupère un service avec ses relations chargées (agents, nb courriers).
     */
    public function findWithDetails(int $id): ?\App\Models\Service;

    // ========================================================================
    // 📊 Agrégations & Dashboard
    // ========================================================================

    /**
     * Retourne le nombre de services groupés par organisation.
     * @return array<int, int> ['organisation_id' => count]
     */
    public function countByOrganisation(): array;

    /**
     * Retourne le nombre d'agents actifs répartis par service.
     * @return array<int, int> ['service_id' => count]
     */
    public function countAgentsByService(): array;

    // ========================================================================
    // ✅ Validation métier
    // ========================================================================

    /**
     * Vérifie si un nom de service est déjà utilisé dans une organisation donnée.
     */
    public function nomExisteDansOrga(string $nom, int $organisationId, ?int $ignoreId = null): bool;
}