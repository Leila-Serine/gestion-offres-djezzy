<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CommandeService
{
    /**
     * Récupère les commandes du client connecté.
     */
    public function getByClient(User $client): Collection
    {
        return Commande::where('id_client', $client->id_user)
            ->with(['boutique', 'lignes.offre'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Crée une commande ainsi que ses lignes.
     *
     * $lignes contient par exemple :
     * [
     *     ['id_offre' => 1, 'quantite' => 10],
     *     ['id_offre' => 3, 'quantite' => 5],
     * ]
     */
    public function createForClient(
        User $client,
        int $idBoutique,
        array $lignes
    ): Commande {
        return DB::transaction(function () use (
            $client,
            $idBoutique,
            $lignes
        ) {
            // Création de la commande.
            $commande = Commande::create([
                'id_client' => $client->id_user,
                'id_boutique' => $idBoutique,

                // Hypothèse de travail :
                // une nouvelle commande commence en attente.
                'statut' => 'en_attente',
            ]);

            // Création des différentes lignes.
            foreach ($lignes as $ligne) {
                $commande->lignes()->create([
                    'id_offre' => $ligne['id_offre'],
                    'quantite' => $ligne['quantite'],
                ]);
            }

            return $commande;
        });
    }

    /**
     * Vérifie que la commande appartient au client.
     */
    public function belongsToClient(
        Commande $commande,
        User $client
    ): bool {
        return $commande->id_client === $client->id_user;
    }
}