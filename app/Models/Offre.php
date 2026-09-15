<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    /**
     * Nom de la clé primaire de la table offres.
     */
    protected $primaryKey = 'id_offre';

    /**
     * Colonnes pouvant être remplies lors de la création
     * ou de la modification d'une offre.
     */
    protected $fillable = [
        'nom_offre',
        'categorie',
    ];
}