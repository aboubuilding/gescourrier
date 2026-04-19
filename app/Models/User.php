<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ── 🎯 Constants pour les rôles ──
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN       = 'admin';
    public const ROLE_CHEF        = 'chef_service';
    public const ROLE_SECRETAIRE  = 'secretaire';
    public const ROLE_AGENT       = 'agent';

    // ── 🎯 Constants pour l'état ──
    public const ETAT_ACTIF    = 1;
    public const ETAT_SUSPENDU = 2;

    // ── 🔐 Mass Assignment ──
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'etat',
        'email_verified_at',
        'derniere_connexion',
    ];

    // ── 🔒 Hidden Fields (exclus des réponses JSON/API) ──
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ── 📦 Attribute Casting ──
    protected $casts = [
        'email_verified_at'  => 'datetime',
        'derniere_connexion' => 'datetime',
        'password'           => 'hashed',
        'etat'               => 'integer',
    ];

    // ── 🎨 Attributes (Accessors/Mutators) ──

    /**
     * Formate le nom complet (prénom + nom) pour l'affichage
     * Ex: "KOFFI Amadou" → "Amadou KOFFI"
     */
    protected function nomComplet(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name, // Personnalise si tu sépares prénom/nom
        );
    }

    /**
     * Retourne le label lisible du rôle
     */
    protected function roleLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match($this->role) {
                self::ROLE_SUPER_ADMIN => 'Super Administrateur',
                self::ROLE_ADMIN       => 'Administrateur',
                self::ROLE_CHEF        => 'Chef de service',
                self::ROLE_SECRETAIRE  => 'Secrétaire',
                self::ROLE_AGENT       => 'Agent',
                default                => 'Utilisateur',
            },
        );
    }

    /**
     * Retourne l'URL de l'avatar (Gravatar ou fallback)
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => 'https://www.gravatar.com/avatar/' .
                md5(strtolower(trim($this->email))) .
                '?d=mp&s=80',
        );
    }

    // ── 🔗 Relations ──

    /**
     * Un utilisateur peut être lié à un agent métier
     */
    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class, 'user_id', 'id');
    }

    /**
     * Courriers affectés à cet utilisateur (via la table agents si besoin)
     * Exemple : si tu as une relation many-to-many via une pivot
     */
    // public function courriersAffectes()
    // {
    //     return $this->belongsToMany(Courrier::class, 'affectations')
    //         ->withPivot('date_affectation', 'note')
    //         ->withTimestamps();
    // }

    // ── 🔍 Scopes (requêtes réutilisables) ──

    /**
     * Scope : utilisateurs actifs uniquement
     */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('etat', self::ETAT_ACTIF);
    }

    /**
     * Scope : utilisateurs suspendus
     */
    public function scopeSuspendus(Builder $query): Builder
    {
        return $query->where('etat', self::ETAT_SUSPENDU);
    }

    /**
     * Scope : filtrer par rôle(s)
     * Usage : User::byRole('admin')->get();
     *         User::byRole(['chef_service', 'secretaire'])->get();
     */
    public function scopeByRole(Builder $query, string|array $roles): Builder
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return $query->whereIn('role', $roles);
    }

    /**
     * Scope : recherche globale (name, email, telephone)
     * Usage : User::search('amadou')->get();
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('telephone', 'like', "%{$term}%");
        });
    }

    /**
     * Scope : connectés récemment (dernière connexion dans les X jours)
     * Usage : User::recentlyActive(7)->get();
     */
    public function scopeRecentlyActive(Builder $query, int $days = 7): Builder
    {
        return $query->whereNotNull('derniere_connexion')
                     ->where('derniere_connexion', '>=', Carbon::now()->subDays($days));
    }

    // ── ✅ Helpers Métier ──

    /**
     * L'utilisateur est-il actif ?
     */
    public function estActif(): bool
    {
        return $this->etat === self::ETAT_ACTIF;
    }

    /**
     * L'utilisateur est-il suspendu ?
     */
    public function estSuspendu(): bool
    {
        return $this->etat === self::ETAT_SUSPENDU;
    }

    /**
     * L'utilisateur a-t-il un rôle donné ?
     */
    public function aRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * L'utilisateur a-t-il l'un des rôles donnés ?
     * Usage : $user->hasAnyRole(['admin', 'super_admin'])
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * L'utilisateur est-il un administrateur (admin ou super_admin) ?
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole([self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
    }

    /**
     * L'utilisateur est-il un super_admin ?
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Enregistrer la dernière connexion
     * Usage : $user->markAsConnected();
     */
    public function markAsConnected(): bool
    {
        return $this->update(['derniere_connexion' => now()]);
    }

    /**
     * Formater la dernière connexion pour l'affichage
     * Usage : $user->formattedLastLogin() → "Il y a 2 heures"
     */
    public function formattedLastLogin(?string $locale = 'fr'): ?string
    {
        if (!$this->derniere_connexion) return null;
        
        return $this->derniere_connexion
            ->locale($locale)
            ->diffForHumans(['short' => true]);
    }

    /**
     * Générer un token de réinitialisation de mot de passe
     * (si tu gères le reset manuellement)
     */
    public function generatePasswordResetToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    // ── 🔐 Overrides Laravel ──

    /**
     * Vérifie si l'utilisateur peut se connecter
     * (étendu pour inclure la vérification de l'état)
     */
    public function canLogin(): bool
    {
        return $this->estActif() 
            && $this->email_verified_at !== null; // Optionnel : exige email vérifié
    }

    /**
     * Get the e-mail address where password reset links are sent
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }
}