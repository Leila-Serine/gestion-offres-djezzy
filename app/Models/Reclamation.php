<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reclamation extends Model
{
    protected $primaryKey = 'id_reclamation';

    protected $fillable = [
        'id_client',
        'objet',
        'message',
        'statut',
    ];

    /**
     * Une réclamation appartient à un client.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_client',
            'id_user'
        );
    }
    public function messages(): HasMany
{
    return $this->hasMany(
        ReclamationMessage::class,
        'id_reclamation',
        'id_reclamation'
    );
}
}