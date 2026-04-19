<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template T of Model
 */
interface BaseRepositoryInterface
{
    // 📖 Lecture
    /** @return Collection<int, T> */
    public function all(array $columns = ['*']): Collection;

    /** @return T|null */
    public function find(int $id): ?Model;

    /** @return T */
    public function findOrFail(int $id): Model;

    // ✍️ Écriture
    /** @return T */
    public function create(array $data): Model;

    /** @return bool */
    public function update(int $id, array $data): bool;

    // 🗑️ État (ta logique etat = 1/2)
    /** @return bool */
    public function supprimer(int $id): bool; // logique → etat = 2

    /** @return bool */
    public function restaurer(int $id): bool; // → etat = 1

    /** @return bool|null */
    public function forceDelete(int $id): ?bool; // physique

    // 🔍 Requête & Pagination
    /** @return \Illuminate\Database\Eloquent\Builder<T> */
    public function query();

    /** @return LengthAwarePaginator */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    // ✅ Utilitaire
    public function exists(int $id): bool;
}