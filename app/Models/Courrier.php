<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Courrier extends Model
{
    use HasFactory;

    // 🔖 Constantes — Types
    public const TYPE_ENTRANT  = 0;
    public const TYPE_SORTANT  = 1;
    public const TYPE_INTERNE  = 2;
    
    // 🔖 Constantes — Priorités
    public const PRIORITE_NORMALE    = 0;
    public const PRIORITE_URGENTE    = 1;
    public const PRIORITE_TRES_URGENTE = 2;
    
    // 🔖 Constantes — Statuts (✅ CORRIGÉ : ajout de STATUT_ENREGISTRE)
    public const STATUT_ENREGISTRE    = 0;  // ✅ AJOUTÉ : courrier créé, pas encore affecté
    public const STATUT_NON_AFFECTE   = 0;  // Alias pour compatibilité
    public const STATUT_AFFECTE       = 1;
    public const STATUT_TRAITE        = 2;
    public const STATUT_ARCHIVE       = 3;  // ✅ Bonus : pour archivage
    
    // 🔖 Constantes — État
    public const ETAT_ACTIF    = 1;
    public const ETAT_SUPPRIME = 2;

    // ⚙️ Configuration
    protected $table = 'courriers';

    protected $fillable = [
        'reference', 'numero', 'type', 'priorite', 'statut',
        'objet', 'description',
        'date_reception', 'date_envoi', 'date_affectation', 'date_traitement',
        'url_fichier', 'fichier_nom_original', 'fichier_mime_type', 'fichier_taille',
        'agent_id', 'service_id', 'utilisateur_id', 'organisation_id',
        'etat'
    ];

    protected $casts = [
        'type' => 'integer',
        'priorite' => 'integer',
        'statut' => 'integer',
        'etat' => 'integer',
        'date_reception' => 'date',
        'date_envoi' => 'date',
        'date_affectation' => 'datetime',
        'date_traitement' => 'datetime',
        'fichier_taille' => 'integer',
    ];

    // 🌍 Global scope : exclut automatiquement les courriers "supprimés"
    protected static function booted(): void
    {
        static::addGlobalScope('etat_actif', function (Builder $builder) {
            $builder->where('etat', self::ETAT_ACTIF);
        });
    }

    // ═══════════════════════════════════════
    // 📋 MÉTHODES POUR DROPDOWNS
    // ═══════════════════════════════════════

    public static function getTypesList(): array
    {
        return [
            self::TYPE_ENTRANT  => 'Entrant',
            self::TYPE_SORTANT  => 'Sortant',
            self::TYPE_INTERNE  => 'Interne',
        ];
    }

    public function getTypeLabel(): string
    {
        return self::getTypesList()[$this->type] ?? 'Inconnu';
    }

    public static function getPrioritesList(): array
    {
        return [
            self::PRIORITE_NORMALE    => 'Normale',
            self::PRIORITE_URGENTE    => 'Urgente',
            self::PRIORITE_TRES_URGENTE => 'Très urgente',
        ];
    }

    public function getPrioriteLabel(): string
    {
        return self::getPrioritesList()[$this->priorite] ?? 'Inconnue';
    }

    public static function getStatutsList(): array
    {
        return [
            self::STATUT_ENREGISTRE  => 'Enregistré',    // ✅ AJOUTÉ
            self::STATUT_AFFECTE     => 'Affecté',
            self::STATUT_TRAITE      => 'Traité',
            self::STATUT_ARCHIVE     => 'Archivé',       // ✅ Bonus
        ];
    }

    public function getStatutLabel(): string
    {
        return self::getStatutsList()[$this->statut] ?? 'Inconnu';
    }

    // Helpers métier
    public function estUrgent(): bool
    {
        return $this->priorite >= self::PRIORITE_URGENTE;
    }

    public function estTraite(): bool
    {
        return $this->statut === self::STATUT_TRAITE;
    }

    public function estEnregistre(): bool
    {
        return $this->statut === self::STATUT_ENREGISTRE;
    }

    // ═══════════════════════════════════════
    // 🔗 RELATIONS
    // ═══════════════════════════════════════

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    // ═══════════════════════════════════════
    // 🗑️ MÉTHODES D'ÉTAT
    // ═══════════════════════════════════════

    public function supprimerLogique(): bool
    {
        return $this->update(['etat' => self::ETAT_SUPPRIME]);
    }

    public function restaurer(): bool
    {
        return $this->update(['etat' => self::ETAT_ACTIF]);
    }
}