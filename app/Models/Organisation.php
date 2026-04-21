<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    use HasFactory;

    // 🔖 Constantes — État
    public const ETAT_ACTIF   = 1;
    public const ETAT_INACTIF = 2;

    // 🔖 Constantes — Types (optionnel, pour référence)
    public const TYPE_GOUVERNEMENTALE = 'gouvernementale';
    public const TYPE_PRIVEE          = 'privee';
    public const TYPE_ONG             = 'ong';
    public const TYPE_INTERNATIONALE  = 'internationale';

    // ⚙️ Configuration
    protected $table = 'organisations';

    protected $fillable = [
        'nom',
        'sigle',
        'type',
        'adresse',
        'telephone',
        'email',
        'etat',
    ];

    protected $casts = [
        'etat' => 'integer',
    ];

    // 🌍 Global scope : exclut automatiquement les organisations inactives
    protected static function booted(): void
    {
        static::addGlobalScope('actif', function (Builder $query) {
            $query->where('etat', self::ETAT_ACTIF);
        });
    }

    // ═══════════════════════════════════════
    // 🔗 RELATIONS (✅ FIX: services() ajouté)
    // ═══════════════════════════════════════

    /**
     * ✅ Une organisation a PLUSIEURS services
     * 
     * Utilisation :
     * - $organisation->services           → Collection de services
     * - $organisation->services()->get()  → Query builder
     * - $organisation->services()->where('nom', 'LIKE', '%IT%')->get()
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'organisation_id');
    }

    /**
     * Une organisation a PLUSIEURS agents
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'organisation_id');
    }

    /**
     * Une organisation est expéditeur de PLUSIEURS courriers
     * (si tu as un champ expediteur_id dans courriers pointant vers organisations)
     */
    public function courriersExpedies(): HasMany
    {
        return $this->hasMany(Courrier::class, 'expediteur_id');
    }

    /**
     * Une organisation est destinataire de PLUSIEURS courriers
     * (si tu as un champ destinataire_id dans courriers pointant vers organisations)
     */
    public function courriersRecus(): HasMany
    {
        return $this->hasMany(Courrier::class, 'destinataire_id');
    }

    // ═══════════════════════════════════════
    // 🎯 HELPERS MÉTIER
    // ═══════════════════════════════════════

    /**
     * Label lisible du type d'organisation
     */
    public function getTypeLabel(): string
    {
        $types = [
            self::TYPE_GOUVERNEMENTALE => 'Gouvernementale',
            self::TYPE_PRIVEE          => 'Privée',
            self::TYPE_ONG             => 'ONG',
            self::TYPE_INTERNATIONALE  => 'Internationale',
        ];
        return $types[$this->type ?? ''] ?? 'Non spécifié';
    }

    /**
     * Vérifie si l'organisation est active
     */
    public function estActive(): bool
    {
        return $this->etat === self::ETAT_ACTIF;
    }

    /**
     * Nom complet avec sigle : "MINFI (Ministère des Finances)"
     */
    public function getNomComplet(): string
    {
        if ($this->sigle && $this->nom) {
            return "{$this->sigle} ({$this->nom})";
        }
        return $this->nom ?? $this->sigle ?? '—';
    }

    /**
     * Compte le nombre de services actifs
     */
    public function getServicesActifsCountAttribute(): int
    {
        // Avec cache pour éviter N+1 queries
        return $this->services()->where('etat', Service::ETAT_ACTIF ?? 1)->count();
    }

    // ═══════════════════════════════════════
    // 🔍 SCOPES DE RECHÊTE
    // ═══════════════════════════════════════

    /**
     * Scope : filtrer par type
     * Usage : Organisation::parType('ong')->get()
     */
    public function scopeParType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : recherche textuelle sur nom/sigle
     * Usage : Organisation::recherche('minfi')->get()
     */
    public function scopeRecherche(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nom', 'LIKE', "%{$term}%")
              ->orWhere('sigle', 'LIKE', "%{$term}%");
        });
    }

    // ═══════════════════════════════════════
    // 🗑️ MÉTHODES D'ÉTAT (déjà présentes)
    // ═══════════════════════════════════════

    public function supprimerLogique(): bool
    {
        return $this->update(['etat' => self::ETAT_INACTIF]);
    }

    public function restaurer(): bool
    {
        return $this->update(['etat' => self::ETAT_ACTIF]);
    }
}