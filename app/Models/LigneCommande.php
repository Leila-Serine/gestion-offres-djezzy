<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommande extends Model
{
    protected $table = 'ligne_commandes';

    protected $primaryKey = 'id_ligne_commande';

    protected $fillable = [
        'id_commande',
        'id_offre',
        'quantite',
        'prix_unitaire',
        'montant_ligne',
    ];

    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
            'prix_unitaire' => 'decimal:2',
            'montant_ligne' => 'decimal:2',
        ];
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(
            Commande::class,
            'id_commande',
            'id_commande'
        );
    }

    public function offre(): BelongsTo
    {
        return $this->belongsTo(
            Offre::class,
            'id_offre',
            'id_offre'
        );
    }
}