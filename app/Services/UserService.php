<?php

namespace App\Services;

use App\Repositories\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UserService
{
    public function __construct(
        protected UserInterface $repo
    ) {}

    // ========================================================================
    // 📋 LISTE & FORMATAGE
    // ========================================================================

    /**
     * Retourne tous les utilisateurs actifs, formatés et prêts pour l'API/Frontend.
     */
    public function liste(array $filtres = []): array
    {
        $query = $this->repo->query()
            ->where('etat', User::ETAT_ACTIF)
            ->latest();

        // Filtres optionnels
        !empty($filtres['nom']) && $query->where('name', 'LIKE', "%{$filtres['nom']}%");
        !empty($filtres['email']) && $query->where('email', 'LIKE', "%{$filtres['email']}%");
        !empty($filtres['role']) && $query->where('role', $filtres['role']);

        return $query->get()->map(fn($u) => $this->formatUser($u))->toArray();
    }

    /**
     * Formate un utilisateur avec ses métadonnées et libellés métier.
     */
    public function formatUser(User $user): array
    {
        return [
            'id'            => $user->id,
            'nom'           => $user->name,
            'email'         => $user->email,
            'role'          => ['code' => $user->role, 'libelle' => $this->getRoleLibelle($user->role)],
            'telephone'     => $user->telephone ?? null,
            'avatar'        => $user->avatar ? asset('storage/' . ltrim($user->avatar, '/')) : null,
            'etat'          => $user->etat === User::ETAT_ACTIF ? 'actif' : 'suspendu',
            'email_verified'=> $user->email_verified_at !== null,
            'derniere_connexion' => $user->derniere_connexion?->format('Y-m-d H:i:s'),
            'created_at'    => $user->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $user->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function getRoleLibelle(string $role): string
    {
        return match($role) {
            'super_admin'  => 'Super Administrateur',
            'admin'        => 'Administrateur',
            'chef_service' => 'Chef de Service',
            'secretaire'   => 'Secrétaire',
            'agent'        => 'Agent',
            default        => 'Utilisateur',
        };
    }

    // ========================================================================
    // ➕ CRUD
    // ========================================================================

    public function creer(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['etat'] = User::ETAT_ACTIF;
            
            // Hashage automatique du mot de passe
            if (empty($data['password'])) {
                throw new InvalidArgumentException('Le champ password est requis.');
            }
            $data['password'] = Hash::make($data['password']);

            return $this->repo->create($data);
        });
    }

    public function mettreAJour(int $id, array $data): User
    {
        // Hashage conditionnel si un nouveau mot de passe est fourni
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $this->repo->update($id, $data);
        return $this->repo->findOrFail($id);
    }

    public function supprimer(int $id): bool { return $this->repo->supprimer($id); }
    public function restaurer(int $id): bool   { return $this->repo->restaurer($id); }

    // ========================================================================
    // 🔐 GESTION DE COMPTE & SÉCURITÉ
    // ========================================================================

    public function assignerRole(int $userId, string $role): bool
    {
        return $this->repo->assignerRole($userId, $role);
    }

    public function changerMotDePasse(int $userId, string $password): bool
    {
        return $this->repo->changerMotDePasse($userId, $password);
    }

    /**
     * À appeler après une authentification réussie (LoginController ou Listener)
     */
    public function enregistrerConnexion(int $userId): bool
    {
        return $this->repo->enregistrerConnexion($userId);
    }

    // ========================================================================
    // 🔍 RECHERCHE & DASHBOARD
    // ========================================================================

    public function rechercher(array $filtres): LengthAwarePaginator
    {
        return $this->repo->rechercher($filtres);
    }

    public function getByRole(string $role): array
    {
        return $this->repo->getByRole($role)
            ->map(fn($u) => $this->formatUser($u))
            ->toArray();
    }

    public function getStats(): array
    {
        return [
            'total_actifs'    => $this->repo->query()->where('etat', User::ETAT_ACTIF)->count(),
            'total_suspendus' => $this->repo->query()->where('etat', User::ETAT_INACTIF)->count(),
            'par_role'        => $this->repo->countByRole(),
        ];
    }
}