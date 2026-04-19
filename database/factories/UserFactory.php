<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{  
  
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // mot de passe par défaut pour les tests
            'remember_token' => Str::random(10),
            'role' => 'agent',           // rôle par défaut
            'etat' => User::ETAT_ACTIF,  // compte actif par défaut
        ];
    }

    // 🔐 États utiles pour les tests
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => ['etat' => User::ETAT_INACTIF]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }

    // 🔧 Helpers pratiques
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password)
        ]);
    }

    public function withRole(string $role): static
    {
        return $this->state(fn (array $attributes) => ['role' => $role]);
    }
}