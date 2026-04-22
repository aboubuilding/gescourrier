<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Import
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ IMPORT CORRECT
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{

   use HasFactory; // 🔑 AJOUTER CE TRAIT
    public const ETAT_ACTIF = 1;
    public const ETAT_INACTIF = 2;

    protected $fillable = ['nom', 'organisation_id', 'etat'];

    protected static function booted(): void
    {
        static::addGlobalScope('actif', fn (Builder $q) => $q->where('etat', self::ETAT_ACTIF));
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function supprimerLogique(): bool
    {
        return $this->update(['etat' => self::ETAT_INACTIF]);
    }

    public function restaurer(): bool
    {
        return $this->update(['etat' => self::ETAT_ACTIF]);
    }

     /**
     * Un service a plusieurs agents
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'service_id');
    }

    public function courriers(): HasMany
{
    return $this->hasMany(Courrier::class, 'service_id');
}
}