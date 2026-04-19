<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Agent;
use App\Models\Service;
use App\Models\User;

class AgentFactory extends Factory
{
    protected $model = Agent::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->optional(0.7)->phoneNumber(),
            'fonction' => fake()->randomElement([
                'Agent de saisie',
                'Gestionnaire courrier',
                'Assistant administratif',
                'Chargé de mission',
                'Secrétaire',
            ]),
            'service_id' => Service::factory(),
            'user_id' => User::factory(),
            'etat' => Agent::ETAT_ACTIF,
        ];
    }

    // 📊 États
    public function inactif(): static
    {
        return $this->state(fn (array $attributes) => ['etat' => Agent::ETAT_INACTIF]);
    }

    // 🔗 Relations
    public function pourService(?Service $service = null): static
    {
        return $this->state(fn (array $attributes) => [
            'service_id' => $service?->id ?? Service::factory(),
        ]);
    }

    public function avecUser(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user?->id ?? User::factory()->withRole('agent'),
        ]);
    }

    // 💼 Fonctions prédéfinies
    public function chef(): static
    {
        return $this->state(fn (array $attributes) => ['fonction' => 'Chef de service']);
    }

    public function secretaire(): static
    {
        return $this->state(fn (array $attributes) => ['fonction' => 'Secrétaire']);
    }

    public function gestionnaire(): static
    {
        return $this->state(fn (array $attributes) => ['fonction' => 'Gestionnaire courrier']);
    }

    // 🎯 Combinaisons pratiques
    public function actifDansService(?Service $service = null): static
    {
        return $this->pourService($service); // actif est déjà le défaut
    }

    public function complet(): static
    {
        return $this->avecUser()->pourService()->chef();
    }
}