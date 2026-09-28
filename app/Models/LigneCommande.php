<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    /**
     * Clé primaire personnalisée de la table ligne_commandes.
     */
    protected $primaryKey = 'id_ligne_commande';

    /**
     * Champs pouvant être remplis lors de la création
     * ou de la modification d'une ligne de commande.
     */
    protected $fillable = [
        'id_commande',
        'id_offre',
        'quantite',
    ];

    /**
     * Commande à laquelle appartient cette ligne.
     */
    public function commande()
    {
        return $this->belongsTo(
            Commande::class,
            'id_commande',
            'id_commande'
        );
    }

    /**
     * Offre concernée par cette ligne.
     */
    public function offre()
    {
        return $this->belongsTo(
            Offre::class,
            'id_offre',
            'id_offre'
        );
    }
}