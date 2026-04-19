<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Courrier;

/**
 * CourrierInterface
 * Contrat pour les opérations métier spécifiques au module "Courrier".
 * Le CRUD de base est déjà couvert par BaseRepositoryInterface.
 */
interface CourrierInterface
{
    // ========================================================================
    // 📋 Workflow métier (Affectation & Traitement)
    // ========================================================================

    /**
     * Affecte un courrier à un agent/service.
     * @return bool
     */
    public function affecter(int $courrierId, int $agentId, int $serviceId): bool;

    /**
     * Marque un courrier comme traité par un utilisateur.
     * @return bool
     */
    public function marquerTraite(int $courrierId, int $userId): bool;

    // ========================================================================
    // 🔍 Recherche & Filtres avancés
    // ========================================================================

    /**
     * Recherche paginée avec filtres dynamiques.
     * @param array{
     *   type?: int,
     *   priorite?: int,
     *   statut?: int,
     *   service_id?: int,
     *   organisation_id?: int,
     *   date_debut?: string,
     *   date_fin?: string,
     *   search?: string
     * } $filtres
     */
    public function rechercher(array $filtres): LengthAwarePaginator;

    /**
     * Récupère les courriers urgents non encore traités.
     * @return Collection<int, Courrier>
     */
    public function getUrgentsNonTraites(): Collection;

    /**
     * Récupère les courriers d'un service (optionnellement filtrés par statut).
     * @return Collection<int, Courrier>
     */
    public function getByService(int $serviceId, ?int $statut = null): Collection;

    // ========================================================================
    // 📎 Gestion des fichiers scannés
    // ========================================================================

    /**
     * Attache un fichier à un courrier existant.
     * @param array{
     *   url_fichier: string,
     *   fichier_nom_original: string,
     *   fichier_mime_type: string,
     *   fichier_taille: int
     * } $fichierData
     * @return bool
     */
    public function attacherFichier(int $courrierId, array $fichierData): bool;

    // ========================================================================
    // 📊 Agrégations / Dashboard
    // ========================================================================

    /**
     * Retourne le nombre de courriers groupés par statut.
     * @return array<int, int> ['0' => 12, '1' => 8, '2' => 4]
     */
    public function countByStatut(): array;

    /**
     * Retourne le nombre de courriers reçus par période.
     * @param string $periode 'day', 'month', 'year'
     * @return array<string, int>
     */
    public function countByPeriode(string $periode = 'month'): array;
}