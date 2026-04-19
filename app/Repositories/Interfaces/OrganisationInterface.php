<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * OrganisationInterface
 * Contrat pour les opérations métier spécifiques au module "Organisation".
 * Le CRUD de base est déjà couvert par BaseRepositoryInterface.
 */
interface OrganisationInterface
{
    // ========================================================================
    // 🔍 Recherche & Filtres
    // ========================================================================

    /**
     * Recherche paginée avec filtres dynamiques.
     * @param array{
     *   nom?: string,
     *   sigle?: string,
     *   type?: int,
     *   etat?: int
     * } $filtres
     */
    public function rechercher(array $filtres): LengthAwarePaginator;

    /**
     * Récupère les organisations filtrées par type (0=externe, 1=interne, 2=gouv, etc.).
     */
    public function getByType(int $type): Collection;

    /**
     * Récupère une organisation avec la liste de ses services rattachés.
     */
    public function findWithServices(int $id): ?\App\Models\Organisation;

    // ========================================================================
    // 📊 Agrégations & Dashboard
    // ========================================================================

    /**
     * Retourne le nombre d'organisations groupées par type.
     * @return array<int, int> ['type' => count]
     */
    public function countByType(): array;

    /**
     * Retourne le nombre total de services rattachés à l'ensemble des organisations.
     */
    public function countTotalServices(): int;

    // ========================================================================
    // ✅ Validation métier
    // ========================================================================

    /**
     * Vérifie si un sigle est déjà utilisé (exclut l'organisation courante lors d'un update).
     */
    public function sigleExiste(string $sigle, ?int $ignoreId = null): bool;
}