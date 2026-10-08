<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaracteristiqueOffre extends Model
{
    protected $table = 'caracteristique_offres';

    protected $primaryKey = 'id_caracteristique';

    protected $fillable = [
        'id_offre',
        'libelle',
        'valeur',
        'unite',
        'ordre',
    ];

    public function offre(): BelongsTo
    {
        return $this->belongsTo(
            Offre::class,
            'id_offre',
            'id_offre'
        );
    }
}