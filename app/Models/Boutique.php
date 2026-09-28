<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boutique extends Model
{
    /**
     * Nom de la clé primaire de la table boutiques.
     *
     * Laravel cherche "id" par défaut.
     * Notre table utilise "id_boutique".
     */
    protected $primaryKey = 'id_boutique';

    /**
     * Colonnes pouvant être remplies lors de la création
     * ou de la modification d'une boutique.
     */
    protected $fillable = [
        'id_client',
        'nom_boutique',
        'wilaya',
        'adresse',
    ];

    /**
     * Client propriétaire de la boutique.
     *
     * Une boutique appartient à un seul utilisateur/client.
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
 * Commandes concernant cette boutique.
 */
public function commandes()
{
    return $this->hasMany(
        Commande::class,
        'id_boutique',
        'id_boutique'
    );
}

    /**
     * Documents concernant cette boutique.
     *
     * Une boutique peut avoir plusieurs documents.
     */
public function documents()
{
    return $this->hasMany(
        Document::class,
        'id_boutique',
        'id_boutique'
    );
}
}