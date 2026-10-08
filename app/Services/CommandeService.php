<?php

namespace App\Services;

use App\Models\Boutique;
use App\Models\Commande;
use App\Models\Offre;
use App\Models\User;
use App\Notifications\CommandeNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CommandeService
{
    public function __construct(
        private DisponibiliteOffreService $disponibiliteOffreService
    ) {
    }

    /**
     * Retourne les commandes d'un client.
     */
    public function getByClient(User $client): Collection
    {
        return Commande::query()
            ->where('id_client', $client->id_user)
            ->with([
                'boutique',
                'lignes.offre',
            ])
            ->latest('id_commande')
            ->get();
    }

    /**
     * Retourne toutes les commandes pour
     * l'espace commercial.
     */
    public function getAllForCommercial(): Collection
    {
        return Commande::query()
            ->with([
                'client',
                'boutique',
                'lignes.offre',
            ])
            ->latest('id_commande')
            ->get();
    }

    /**
     * Crée une commande client.
     *
     * Si toutes les quantités sont disponibles :
     * - la commande devient "soumise"
     * - le stock est réservé immédiatement.
     *
     * Sinon :
     * - la commande devient "en_attente_stock"
     * - aucun stock n'est retiré.
     */
    public function createForClient(
        User $client,
        Boutique $boutique,
        array $lignes
    ): Commande {
        $commande = DB::transaction(
            function () use (
                $client,
                $boutique,
                $lignes
            ) {
                $zone = $boutique->wilaya;

                $problemes = [];

                /*
                 * On conserve ici les stocks verrouillés.
                 * Ils ne seront décrémentés que si
                 * TOUTES les lignes sont disponibles.
                 */
                $stocks = [];
                $offresCommande = [];
$montantTotal = 0;

                foreach ($lignes as $ligne) {

                    $offre = Offre::query()
                        ->findOrFail(
                            $ligne['id_offre']
                        );
$prixUnitaire = (float) $offre->prix;

$montantLigne =
    $prixUnitaire
    * (int) $ligne['quantite'];

$montantTotal += $montantLigne;

/*
 * On garde une copie des données tarifaires
 * qui seront utilisées lors de la création
 * de la ligne de commande.
 */
$offresCommande[$offre->id_offre] = [
    'offre' => $offre,
    'prix_unitaire' => $prixUnitaire,
    'montant_ligne' => $montantLigne,
];
                    $disponibilite =
                        $this
                            ->disponibiliteOffreService
                            ->getLocked(
                                $offre->id_offre,
                                $zone
                            );

                    if (! $disponibilite) {

                        $problemes[] =
                            "Aucune disponibilité enregistrée pour "
                            . $offre->nom_offre
                            . " dans la zone "
                            . $zone
                            . ".";

                        continue;
                    }

                    if (
                        $ligne['quantite']
                        > $disponibilite->quantite_disponible
                    ) {
                        $problemes[] =
                            "Disponibilité insuffisante pour "
                            . $offre->nom_offre
                            . " dans la zone "
                            . $zone
                            . ".";

                        continue;
                    }

                    $stocks[
                        $offre->id_offre
                    ] = $disponibilite;
                }

                $stockDisponible =
                    empty($problemes);

                $commande = Commande::create([
                    'id_client' =>
                        $client->id_user,

                    'id_boutique' =>
                        $boutique->id_boutique,

                    'statut' =>
                        $stockDisponible
                            ? 'soumise'
                            : 'en_attente_stock',

                    'motif_statut' =>
                        $stockDisponible
                            ? null
                            : implode(
                                ' ',
                                $problemes
                            ),

                    'stock_reserve' =>
                        $stockDisponible,
                        'montant_total' => $montantTotal,
                ]);

                /*
                 * Création des lignes.
                 */
                foreach ($lignes as $ligne) {

                   $tarif =
    $offresCommande[
        $ligne['id_offre']
    ];

$commande->lignes()->create([
    'id_offre' =>
        $ligne['id_offre'],

    'quantite' =>
        $ligne['quantite'],

    /*
     * Snapshot du prix.
     */
    'prix_unitaire' =>
        $tarif['prix_unitaire'],

    'montant_ligne' =>
        $tarif['montant_ligne'],
]);
                }

                /*
                 * Réservation réelle du stock uniquement
                 * si toutes les lignes sont disponibles.
                 */
                if ($stockDisponible) {

                    foreach ($lignes as $ligne) {

                        $stocks[
                            $ligne['id_offre']
                        ]->decrement(
                            'quantite_disponible',
                            $ligne['quantite']
                        );
                    }
                }

                return $commande->load([
                    'client',
                    'boutique',
                    'lignes.offre',
                ]);
            }
        );

        /*
         * Notification des commerciaux.
         *
         * La transaction est déjà terminée :
         * on ne notifie donc que si la commande
         * a réellement été créée.
         */
        $commercials = User::query()
            ->where('role', 'commercial')
            ->get();

        foreach ($commercials as $commercial) {

            if (
                $commande->statut
                === 'soumise'
            ) {
                $commercial->notify(
                    new CommandeNotification(
                        'Nouvelle commande',
                        'Le client '
                        . $client->name
                        . ' a soumis la commande #'
                        . $commande->id_commande
                        . '.',
                        $commande->id_commande
                    )
                );
            } else {
                $commercial->notify(
                    new CommandeNotification(
                        'Commande en attente de stock',
                        'La commande #'
                        . $commande->id_commande
                        . ' du client '
                        . $client->name
                        . ' est en attente de stock.',
                        $commande->id_commande
                    )
                );
            }
        }

        return $commande;
    }

    /**
     * Vérifie qu'une commande appartient
     * bien au client connecté.
     */
    public function belongsToClient(
        Commande $commande,
        User $client
    ): bool {
        return $commande->id_client
            === $client->id_user;
    }

    /**
     * Validation d'une commande par
     * le commercial.
     */
    public function validateCommande(
        Commande $commande
    ): Commande {
        $resultat = DB::transaction(
            function () use ($commande) {

                /*
                 * Verrouille également la commande pour
                 * empêcher deux traitements simultanés.
                 */
                $commande = Commande::query()
                    ->where(
                        'id_commande',
                        $commande->id_commande
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    in_array(
                        $commande->statut,
                        [
                            'validee',
                            'refusee',
                        ],
                        true
                    )
                ) {
                    throw new RuntimeException(
                        'Cette commande a déjà été traitée.'
                    );
                }

                /*
                 * Si le stock avait déjà été réservé
                 * lors de la création, il ne faut surtout
                 * pas le retirer une deuxième fois.
                 */
                if ($commande->stock_reserve) {

                    $commande->update([
                        'statut' => 'validee',
                        'motif_statut' => null,
                    ]);

                    return [
                        'success' => true,
                        'commande' =>
                            $commande->refresh(),
                        'message' => null,
                    ];
                }

                /*
                 * Sinon, la commande était en attente.
                 * On vérifie si le stock est désormais
                 * disponible.
                 */
                $commande->load([
                    'boutique',
                    'lignes.offre',
                ]);

                $zone =
                    $commande->boutique->wilaya;

                $problemes = [];
                $stocks = [];

                foreach (
                    $commande->lignes
                    as $ligne
                ) {
                    $disponibilite =
                        $this
                            ->disponibiliteOffreService
                            ->getLocked(
                                $ligne->id_offre,
                                $zone
                            );

                    if (! $disponibilite) {

                        $problemes[] =
                            "Aucune disponibilité enregistrée pour "
                            . $ligne->offre->nom_offre
                            . " dans la zone "
                            . $zone
                            . ".";

                        continue;
                    }

                    if (
                        $ligne->quantite
                        > $disponibilite
                            ->quantite_disponible
                    ) {
                        $problemes[] =
                            "Disponibilité insuffisante pour "
                            . $ligne->offre->nom_offre
                            . " dans la zone "
                            . $zone
                            . ".";

                        continue;
                    }

                    $stocks[
                        $ligne->id_offre
                    ] = $disponibilite;
                }

                /*
                 * Toujours pas assez de stock :
                 * la commande reste en attente.
                 */
                if (! empty($problemes)) {

                    $commande->update([
                        'statut' =>
                            'en_attente_stock',

                        'motif_statut' =>
                            implode(
                                ' ',
                                $problemes
                            ),

                        'stock_reserve' =>
                            false,
                    ]);

                    return [
                        'success' => false,
                        'commande' =>
                            $commande->refresh(),
                        'message' =>
                            'Le stock disponible reste insuffisant.',
                    ];
                }

                /*
                 * Le stock est maintenant suffisant.
                 */
                foreach (
                    $commande->lignes
                    as $ligne
                ) {
                    $stocks[
                        $ligne->id_offre
                    ]->decrement(
                        'quantite_disponible',
                        $ligne->quantite
                    );
                }

                $commande->update([
                    'statut' => 'validee',
                    'motif_statut' => null,
                    'stock_reserve' => true,
                ]);

                return [
                    'success' => true,
                    'commande' =>
                        $commande->refresh(),
                    'message' => null,
                ];
            }
        );

        /*
         * Important :
         * le RuntimeException est lancé APRÈS
         * la transaction.
         *
         * Cela évite d'annuler le motif mis à jour
         * dans la base.
         */
        if (! $resultat['success']) {
            throw new RuntimeException(
                $resultat['message']
            );
        }

        $commande =
            $resultat['commande'];

        $commande->load('client');

        /*
         * 🔔 Notification au client.
         */
        $commande->client->notify(
            new CommandeNotification(
                'Commande validée',
                'Votre commande #'
                . $commande->id_commande
                . ' a été validée.',
                $commande->id_commande
            )
        );

        return $commande;
    }

    /**
     * Refus d'une commande.
     *
     * Si du stock avait été réservé,
     * il est remis à disposition.
     */
    public function refuseCommande(
        Commande $commande,
        string $motif
    ): Commande {
        $commande = DB::transaction(
            function () use (
                $commande,
                $motif
            ) {
                $commande = Commande::query()
                    ->where(
                        'id_commande',
                        $commande->id_commande
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    in_array(
                        $commande->statut,
                        [
                            'validee',
                            'refusee',
                        ],
                        true
                    )
                ) {
                    throw new RuntimeException(
                        'Cette commande a déjà été traitée.'
                    );
                }

                $commande->load([
                    'boutique',
                    'lignes.offre',
                ]);

                /*
                 * Si le stock avait été réservé,
                 * on le restitue.
                 */
                if ($commande->stock_reserve) {

                    $zone =
                        $commande
                            ->boutique
                            ->wilaya;

                    foreach (
                        $commande->lignes
                        as $ligne
                    ) {
                        $disponibilite =
                            $this
                                ->disponibiliteOffreService
                                ->getLocked(
                                    $ligne->id_offre,
                                    $zone
                                );

                        if ($disponibilite) {
                            $disponibilite->increment(
                                'quantite_disponible',
                                $ligne->quantite
                            );
                        }
                    }
                }

                $commande->update([
                    'statut' => 'refusee',
                    'motif_statut' => $motif,
                    'stock_reserve' => false,
                ]);

                return $commande->refresh();
            }
        );

        $commande->load('client');

        /*
         * 🔔 Notification au client.
         */
        $commande->client->notify(
            new CommandeNotification(
                'Commande refusée',
                'Votre commande #'
                . $commande->id_commande
                . ' a été refusée. Motif : '
                . $motif,
                $commande->id_commande
            )
        );

        return $commande;
    }
}