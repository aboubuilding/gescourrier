<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Import
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agent extends Model
{

    use HasFactory; // 🔑 AJOUTER CE TRAIT
    public const ETAT_ACTIF = 1;
    public const ETAT_INACTIF = 2;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone',
        'fonction', 'service_id', 'user_id', 'etat'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('actif', fn (Builder $q) => $q->where('etat', self::ETAT_ACTIF));
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supprimerLogique(): bool
    {
        return $this->update(['etat' => self::ETAT_INACTIF]);
    }

    public function restaurer(): bool
    {
        return $this->update(['etat' => self::ETAT_ACTIF]);
    }
}