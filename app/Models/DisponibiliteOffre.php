<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibiliteOffre extends Model
{
    protected $table = 'disponibilite_offres';

    protected $primaryKey = 'id_disponibilite';

    protected $fillable = [
        'id_offre',
        'zone',
        'quantite_disponible',
    ];

    protected $casts = [
        'quantite_disponible' => 'integer',
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
