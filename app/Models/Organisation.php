<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Import
use Illuminate\Database\Eloquent\Builder;

class Organisation extends Model
{
       use HasFactory; // 🔑 AJOUTER CE TRAIT
    public const ETAT_ACTIF = 1;
    public const ETAT_INACTIF = 2;

    protected $fillable = [
        'nom', 'sigle', 'type', 'adresse', 'telephone', 'email', 'etat'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('actif', fn (Builder $q) => $q->where('etat', self::ETAT_ACTIF));
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