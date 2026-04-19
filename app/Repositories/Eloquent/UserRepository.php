<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Retourne l'instance typée du modèle (pour l'autocomplétion)
     */
    public function getModel(): User
    {
        return $this->model instanceof User ? $this->model : throw new \RuntimeException('Modèle incorrect');
    }

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->query()
            ->when($filtres['nom'] ?? null, fn($q, $v) => $q->where('name', 'LIKE', "%{$v}%"))
            ->when($filtres['email'] ?? null, fn($q, $v) => $q->where('email', 'LIKE', "%{$v}%"))
            ->when($filtres['role'] ?? null, fn($q, $v) => $q->where('role', $v))
            ->when($filtres['etat'] ?? null, fn($q, $v) => $q->where('etat', $v))
            ->latest()
            ->paginate(15);
    }

    public function getByRole(string $role): Collection
    {
        return $this->query()->where('role', $role)->get();
    }

    public function changerMotDePasse(int $userId, string $nouveauPassword): bool
    {
        return $this->update($userId, ['password' => Hash::make($nouveauPassword)]);
    }

    public function assignerRole(int $userId, string $role): bool
    {
        return $this->update($userId, ['role' => $role]);
    }

    public function enregistrerConnexion(int $userId): bool
    {
        return $this->update($userId, ['derniere_connexion' => now()]);
    }

    public function countByRole(): array
    {
        return $this->query()
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();
    }

    public function emailExiste(string $email, ?int $ignoreId = null): bool
    {
        $query = $this->query()->where('email', $email);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}