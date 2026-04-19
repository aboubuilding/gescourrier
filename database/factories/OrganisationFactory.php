<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organisation;

class OrganisationFactory extends Factory
{

 
    protected $model = Organisation::class;

    public function definition(): array
    {
        return [
            'nom'     => fake()->company(),
            'sigle'   => strtoupper(fake()->unique()->lexify('???')), // ex: MIN, RH, DSI
            'type'    => fake()->randomElement([0, 1, 2, 3, 4]), // 0=externe, 1=interne, 2=gouv, 3=privé, 4=ONG
            'adresse' => fake()->optional(0.7)->address(),
            'telephone' => fake()->optional(0.6)->phoneNumber(),
            'email'   => fake()->optional(0.5)->safeEmail(),
            'etat'    => Organisation::ETAT_ACTIF,
        ];
    }

    // 🏢 Types d'organisation
    public function gouvernementale(): static
    {
        return $this->state(fn (array $a) => ['type' => 2]);
    }

    public function privee(): static
    {
        return $this->state(fn (array $a) => ['type' => 3]);
    }

    public function ong(): static
    {
        return $this->state(fn (array $a) => ['type' => 4]);
    }

    public function interne(): static
    {
        return $this->state(fn (array $a) => ['type' => 1]);
    }

    public function externe(): static
    {
        return $this->state(fn (array $a) => ['type' => 0]);
    }

    // 📊 État
    public function inactif(): static
    {
        return $this->state(fn (array $a) => ['etat' => Organisation::ETAT_INACTIF]);
    }

    // 📞 Coordonnées complètes (utile pour les jeux de données réalistes)
    public function avecCoordonnees(): static
    {
        return $this->state(fn (array $a) => [
            'adresse'   => fake()->address(),
            'telephone' => fake()->phoneNumber(),
            'email'     => fake()->safeEmail(),
        ]);
    }
}