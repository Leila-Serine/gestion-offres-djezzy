<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReclamationMessage extends Model
{
    protected $primaryKey = 'id_message';

    protected $fillable = [
        'id_reclamation',
        'id_auteur',
        'contenu',
    ];

    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(
            Reclamation::class,
            'id_reclamation',
            'id_reclamation'
        );
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_auteur',
            'id_user'
        );
    }
}