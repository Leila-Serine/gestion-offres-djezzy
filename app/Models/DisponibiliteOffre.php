<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibiliteOffre extends Model
{
    /*
     * Clé primaire personnalisée.
     */
    protected $primaryKey = 'id_disponibilite';

    /*
     * Champs autorisés pour la création / modification.
     */
    protected $fillable = [
        'id_offre',
        'zone',
        'quantite_disponible',
    ];

    /*
     * Une disponibilité appartient à une offre.
     */
    public function offre(): BelongsTo
    {
        return $this->belongsTo(
            Offre::class,
            'id_offre',
            'id_offre'
        );
    }
}