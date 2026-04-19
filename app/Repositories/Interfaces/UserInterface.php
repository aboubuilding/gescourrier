<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * UserInterface
 * Contrat pour les opérations métier spécifiques au module "User".
 * Le CRUD de base (find, create, update, supprimer, restaurer) est géré par BaseRepositoryInterface.
 */
interface UserInterface
{
    // ========================================================================
    // 🔍 Recherche & Filtres
    // ========================================================================

    /**
     * Recherche paginée avec filtres dynamiques.
     * @param array{
     *   nom?: string,
     *   email?: string,
     *   role?: string,
     *   etat?: int
     * } $filtres
     */
    public function rechercher(array $filtres): LengthAwarePaginator;

    /**
     * Récupère les utilisateurs filtrés par rôle.
     */
    public function getByRole(string $role): Collection;

    // ========================================================================
    // 🔐 Gestion de compte & Sécurité
    // ========================================================================

    /**
     * Change le mot de passe d'un utilisateur (hashage automatique).
     * @return bool
     */
    public function changerMotDePasse(int $userId, string $nouveauPassword): bool;

    /**
     * Attribue ou met à jour le rôle d'un utilisateur.
     * @return bool
     */
    public function assignerRole(int $userId, string $role): bool;

    /**
     * Met à jour la date de dernière connexion (utile pour middleware/listeners).
     * @return bool
     */
    public function enregistrerConnexion(int $userId): bool;

    // ========================================================================
    // 📊 Agrégations & Dashboard
    // ========================================================================

    /**
     * Retourne le nombre d'utilisateurs groupés par rôle.
     * @return array<string, int> ['admin' => 2, 'agent' => 15, ...]
     */
    public function countByRole(): array;

    // ========================================================================
    // ✅ Validation métier
    // ========================================================================

    /**
     * Vérifie si un email est déjà utilisé (exclut l'utilisateur courant lors d'un update).
     */
    public function emailExiste(string $email, ?int $ignoreId = null): bool;
}