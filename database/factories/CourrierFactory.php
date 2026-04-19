<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Closure;
use App\Models\Courrier;
use App\Models\User;
use App\Models\Service;
use App\Models\Organisation;

class CourrierFactory extends Factory
{
    protected $model = Courrier::class;

    public function definition(): array
    {
        return [
            // 🔖 Identifiants métier
            'reference' => fake()->unique()->bothify('REF-####-##'),
            'numero' => fake()->unique()->numerify('N°#######'),
            
            // 📊 Classification
            'type' => fake()->randomElement([Courrier::TYPE_ENTRANT, Courrier::TYPE_SORTANT, Courrier::TYPE_INTERNE]),
            'priorite' => fake()->randomElement([Courrier::PRIORITE_NORMALE, Courrier::PRIORITE_URGENTE]),
            'statut' => Courrier::STATUT_NON_AFFECTE,
            
            // 📝 Contenu
            'objet' => fake()->sentence(4),
            'description' => fake()->optional(0.7)->paragraph(),
            
            // 📅 Dates
            'date_reception' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'date_envoi' => null,
            'date_affectation' => null,
            'date_traitement' => null,
            
            // 📎 Fichier (par défaut : pas de fichier)
            'url_fichier' => null,
            'fichier_nom_original' => null,
            'fichier_mime_type' => null,
            'fichier_taille' => null,
            
            // 🔗 Relations (null par défaut, à définir via states)
            'agent_id' => null,
            'service_id' => null,
            'utilisateur_id' => null,
            'organisation_id' => null,
            
            
            // 🗑️ État
            'etat' => Courrier::ETAT_ACTIF,
        ];
    }

    // ========================================================================
    // 📥 Types de courrier
    // ========================================================================

    public function entrant(): static
    {
        return $this->state(fn (array $a) => ['type' => Courrier::TYPE_ENTRANT]);
    }

    public function sortant(): static
    {
        return $this->state(fn (array $a) => [
            'type' => Courrier::TYPE_SORTANT,
            'date_envoi' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }

    public function interne(): static
    {
        return $this->state(fn (array $a) => ['type' => Courrier::TYPE_INTERNE]);
    }

    // ========================================================================
    // 🚨 Priorités
    // ========================================================================

    public function urgent(): static
    {
        return $this->state(fn (array $a) => ['priorite' => Courrier::PRIORITE_URGENTE]);
    }

    public function tresUrgent(): static
    {
        return $this->state(fn (array $a) => ['priorite' => Courrier::PRIORITE_TRES_URGENTE]);
    }

    // ========================================================================
    // 📊 Statuts & Workflow
    // ========================================================================

    /**
     * Marque le courrier comme affecté
     * @param int|Closure|null $agentId ID de l'agent OU closure résolue dynamiquement
     * @param int|Closure|null $serviceId ID du service OU closure résolue dynamiquement
     */
    public function affecte(int|Closure|null $agentId = null, int|Closure|null $serviceId = null): static
    {
        return $this->state(function (array $attributes) use ($agentId, $serviceId) {
            // Résolution lazy des closures
            $resolvedAgentId = $agentId instanceof Closure ? $agentId($attributes) : $agentId;
            $resolvedServiceId = $serviceId instanceof Closure ? $serviceId($attributes) : $serviceId;
            
            return [
                'statut' => Courrier::STATUT_AFFECTE,
                'agent_id' => $resolvedAgentId ?? User::factory(),
                'service_id' => $resolvedServiceId ?? Service::factory(),
                'date_affectation' => now(),
            ];
        });
    }

    /**
     * Marque le courrier comme traité
     * @param int|Closure|null $utilisateurId ID de l'utilisateur OU closure
     */
    public function traite(int|Closure|null $utilisateurId = null): static
    {
        return $this->state(function (array $attributes) use ($utilisateurId) {
            $resolvedUserId = $utilisateurId instanceof Closure ? $utilisateurId($attributes) : $utilisateurId;
            
            return [
                'statut' => Courrier::STATUT_TRAITE,
                'date_traitement' => now(),
                'utilisateur_id' => $resolvedUserId ?? User::factory(),
            ];
        });
    }

    // ========================================================================
    // 📎 Gestion des fichiers
    // ========================================================================

    /**
     * Ajoute un fichier scanné au courrier
     * @param string|null $nom Nom original du fichier (généré si null)
     * @param string $mime Type MIME (défaut: application/pdf)
     * @param int|null $taille Taille en octets (aléatoire si null)
     */
    public function avecFichier(string $nom = null, string $mime = 'application/pdf', int $taille = null): static
    {
        return $this->state(fn (array $a) => [
            'url_fichier' => 'courriers/' . fake()->uuid() . '.' . pathinfo($mime, PATHINFO_EXTENSION),
            
            // ✅ Noms de fichiers valides avec Faker (pas de fileName() qui n'existe pas)
            'fichier_nom_original' => $nom ?? sprintf(
                '%s_%s_%s.%s',
                fake()->slug(),
                fake()->dateTimeThisYear()->format('Ymd'),
                fake()->numerify('####'),
                pathinfo($mime, PATHINFO_EXTENSION) ?: 'pdf'
            ),
            
            'fichier_mime_type' => $mime,
            'fichier_taille' => $taille ?? fake()->numberBetween(10000, 5000000), // 10 Ko à 5 Mo
        ]);
    }

    // ========================================================================
    // 🏢 Relations
    // ========================================================================

    public function pourOrganisation(?Organisation $org = null): static
    {
        return $this->state(fn (array $a) => [
            'organisation_id' => $org?->id ?? Organisation::factory(),
        ]);
    }

    public function pourService(?Service $service = null): static
    {
        return $this->state(fn (array $a) => [
            'service_id' => $service?->id ?? Service::factory(),
        ]);
    }

    // ========================================================================
    // 🗑️ État (suppression logique)
    // ========================================================================

    public function supprime(): static
    {
        return $this->state(fn (array $a) => ['etat' => Courrier::ETAT_SUPPRIME]);
    }

    // ========================================================================
    // 🎯 Combinaisons pratiques (syntactic sugar)
    // ========================================================================

    public function entrantUrgent(): static
    {
        return $this->entrant()->urgent();
    }

    public function sortantTraite(): static
    {
        return $this->sortant()->traite();
    }

    /**
     * Courrier complet : entrant + urgent + fichier + affecté + lié à une orga
     */
    public function complet(): static
    {
        return $this->entrant()
                    ->urgent()
                    ->avecFichier()
                    ->affecte()
                    ->pourOrganisation();
    }

   
}