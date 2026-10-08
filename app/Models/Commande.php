<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    /**
     * Clé primaire personnalisée de la table commandes.
     */
    protected $primaryKey = 'id_commande';

    /**
     * Champs pouvant être remplis lors de la création
     * ou de la modification d'une commande.
     */
    protected $fillable = [
    'id_client',
    'id_boutique',
    'statut',
    'motif_statut',
    'stock_reserve',
    'montant_total',
];

protected function casts(): array
{
    return [
        'stock_reserve' => 'boolean',
        'montant_total' => 'decimal:2',
    ];
}

    /**
     * Client ayant créé la commande.
     *
     * Une commande appartient à un seul client.
     */
    public function client()
    {
        return $this->belongsTo(
            User::class,
            'id_client',
            'id_user'
        );
    }

    /**
     * Boutique concernée par la commande.
     *
     * Une commande appartient à une seule boutique.
     */
    public function boutique()
    {
        return $this->belongsTo(
            Boutique::class,
            'id_boutique',
            'id_boutique'
        );
    }
    /**
 * Lignes appartenant à cette commande.
 */
public function lignes()
{
    return $this->hasMany(
        LigneCommande::class,
        'id_commande',
        'id_commande'
    );
}
}