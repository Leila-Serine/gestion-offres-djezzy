<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offre extends Model
{
    use HasFactory;

    protected $table = 'offres';

    protected $primaryKey = 'id_offre';

    protected $fillable = [
        'nom_offre',
        'categorie',
        'prix',
        'statut',
        'mode_stock_zero',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
    ];

    public function caracteristiques(): HasMany
    {
        return $this->hasMany(
            CaracteristiqueOffre::class,
            'id_offre',
            'id_offre'
        )->orderBy('ordre');
    }

    public function disponibilites(): HasMany
    {
        return $this->hasMany(
            DisponibiliteOffre::class,
            'id_offre',
            'id_offre'
        )->orderBy('zone');
    }

    public function lignesCommande(): HasMany
    {
        return $this->hasMany(
            LigneCommande::class,
            'id_offre',
            'id_offre'
        );
    }
}
