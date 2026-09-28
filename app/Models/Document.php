<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $primaryKey = 'id_document';

    protected $fillable = [
        'id_user',
        'id_boutique',
        'type_document',
        'chemin_fichier',
    ];

    /**
     * Client propriétaire du document.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Boutique concernée par le document.
     *
     * Cette relation peut être NULL
     * pour un document concernant le compte.
     */
    public function boutique()
    {
        return $this->belongsTo(
            Boutique::class,
            'id_boutique',
            'id_boutique'
        );
    }
}