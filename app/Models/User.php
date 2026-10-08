<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'php artisan migrate',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Clé primaire personnalisée.
     */
    protected $primaryKey = 'id_user';

    /**
     * Boutiques appartenant au client.
     */
    public function boutiques()
    {
        return $this->hasMany(
            Boutique::class,
            'id_client',
            'id_user'
        );
    }
    
    /**
 * Commandes passées par le client.
 */
public function commandes()
{
    return $this->hasMany(
        Commande::class,
        'id_client',
        'id_user'
    );
}

    /**
     * Conversion automatique de certains attributs.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Documents appartenant au client.
     */     
    public function documents()
{
    return $this->hasMany(
        Document::class,
        'id_user',
        'id_user'
    );
}
/**
 * Réclamations déposées par le client.
 */
public function reclamations(): HasMany
{
    return $this->hasMany(
        Reclamation::class,
        'id_client',
        'id_user'
    );
}
}