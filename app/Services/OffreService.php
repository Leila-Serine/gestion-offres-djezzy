<?php

namespace App\Services;

use App\Models\Offre;
use Illuminate\Database\Eloquent\Collection;

class OffreService
{
    /**
     * Récupère toutes les offres enregistrées.
     */
    public function getAll(): Collection
    {
        return Offre::all();
    }
    /**
 * Crée une nouvelle offre.
 */
public function create(array $data): Offre
{
    return Offre::create($data);
}/**
 * Modifie une offre existante.
 */
public function update(Offre $offre, array $data): Offre
{
    $offre->update($data);

    return $offre;
    
}
/**
 * Supprime une offre existante.
 */
public function delete(Offre $offre): void
{
    $offre->delete();
}
}