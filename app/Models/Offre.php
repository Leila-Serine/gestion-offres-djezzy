<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offre extends Model
{
    protected $primaryKey = 'id_offre';

    protected $fillable = [
        'nom_offre',
        'categorie',
        'prix',
        'mode_stock_zero',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
        ];
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(
            LigneCommande::class,
            'id_offre',
            'id_offre'
        );
    }

    /**
     * Alias conservé pour compatibilité avec les tests
     * et le reste du projet.
     */
    public function lignesCommande(): HasMany
    {
        return $this->hasMany(
            LigneCommande::class,
            'id_offre',
            'id_offre'
        );
    }

    public function disponibilites(): HasMany
    {
        return $this->hasMany(
            DisponibiliteOffre::class,
            'id_offre',
            'id_offre'
        );
    }

    public function caracteristiques(): HasMany
    {
        return $this->hasMany(
            CaracteristiqueOffre::class,
            'id_offre',
            'id_offre'
        )->orderBy('ordre');
    }

    public function scopeActive($query)
    {
        return $query->where('statut', 'active');
    }

    public function estActive(): bool
    {
        return $this->statut === 'active';
    }

    public function estSuspendue(): bool
    {
        return $this->statut === 'suspendue';
    }
}
