<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organisation;
use App\Models\Service;

class ServiceFactory extends Factory
{
  
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'nom' => ucwords(fake()->words(2, true)), // ex: "Ressources Humaines", "Direction Financière"
            'organisation_id' => Organisation::factory(),
            'etat' => Service::ETAT_ACTIF,
        ];
    }

    // 📊 États
    public function inactif(): static
    {
        return $this->state(fn (array $attributes) => ['etat' => Service::ETAT_INACTIF]);
    }

    public function pourOrganisation(?Organisation $organisation = null): static
    {
        return $this->state(fn (array $attributes) => [
            'organisation_id' => $organisation?->id ?? Organisation::factory(),
        ]);
    }

    // 🎯 Combinaison pratique
    public function actifDansOrganisation(?Organisation $org = null): static
    {
        return $this->pourOrganisation($org); // actif est déjà le défaut
    }
}