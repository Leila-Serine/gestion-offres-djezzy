<?php

namespace App\Services;

use App\Models\DisponibiliteOffre;
use App\Models\Offre;
use Illuminate\Database\Eloquent\Collection;

class DisponibiliteOffreService
{
    /**
     * Récupère toutes les disponibilités
     * d'une zone.
     */
    public function getByZone(
        string $zone
    ): Collection {
        return DisponibiliteOffre::query()
            ->where('zone', $zone)
            ->with('offre')
            ->get();
    }


    /**
     * Récupère la disponibilité d'une offre
     * dans une zone précise.
     */
    public function getForOfferAndZone(
        Offre $offre,
        string $zone
    ): ?DisponibiliteOffre {
        return DisponibiliteOffre::query()
            ->where(
                'id_offre',
                $offre->id_offre
            )
            ->where('zone', $zone)
            ->first();
    }


    /**
     * Version utilisée pendant la création
     * d'une commande.
     *
     * lockForUpdate empêche deux commandes
     * concurrentes de réserver le même stock
     * simultanément.
     *
     * Cette méthode doit être utilisée
     * à l'intérieur d'une transaction SQL.
     */
    public function getLocked(
        int $idOffre,
        string $zone
    ): ?DisponibiliteOffre {
        return DisponibiliteOffre::query()
            ->where('id_offre', $idOffre)
            ->where('zone', $zone)
            ->lockForUpdate()
            ->first();
    }
}