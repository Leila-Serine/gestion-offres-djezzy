<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Commande;
use App\Models\DisponibiliteOffre;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommercialCommandeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crée un commercial de test.
     */
    private function createCommercial(): User
    {
        return User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);
    }

    /**
     * Crée un client vérifié.
     */
    private function createClient(): User
    {
        $client = User::create([
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $client->markEmailAsVerified();

        return $client;
    }

    /**
     * Crée une boutique à Oran pour le client.
     */
    private function createBoutique(
        User $client
    ): Boutique {
        return Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Test',
            'wilaya' => 'Oran',
            'adresse' => 'Centre-ville',
        ]);
    }

    /**
     * Crée une offre de test.
     */
    private function createOffre(
        string $nom = 'Offre Test'
    ): Offre {
        return Offre::create([
            'nom_offre' => $nom,
            'categorie' => 'Internet',
        ]);
    }

    /**
     * Crée une disponibilité pour une offre.
     */
    private function createDisponibilite(
        Offre $offre,
        int $quantite,
        string $zone = 'Oran'
    ): DisponibiliteOffre {
        return DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => $zone,
            'quantite_disponible' => $quantite,
        ]);
    }

    /**
     * Crée une commande avec une ligne.
     */
    private function createCommande(
        User $client,
        Boutique $boutique,
        Offre $offre,
        int $quantite,
        string $statut,
        bool $stockReserve,
        ?string $motif = null
    ): Commande {
        $commande = Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => $statut,
            'motif_statut' => $motif,
            'stock_reserve' => $stockReserve,
        ]);

        $commande->lignes()->create([
            'id_offre' => $offre->id_offre,
            'quantite' => $quantite,
        ]);

        return $commande;
    }


    /**
     * CAS 1
     *
     * Une commande soumise possède déjà
     * un stock réservé.
     *
     * Le commercial peut donc la valider
     * sans décrémenter le stock une deuxième fois.
     */
    public function test_un_commercial_peut_valider_une_commande_soumise(): void
    {
        $commercial = $this->createCommercial();

        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $offre = $this->createOffre();

        /*
         * On considère que 30 unités ont déjà
         * été réservées auparavant.
         *
         * Stock initial hypothétique : 100
         * Stock actuel : 70
         */
        $this->createDisponibilite(
            $offre,
            70
        );

        $commande = $this->createCommande(
            client: $client,
            boutique: $boutique,
            offre: $offre,
            quantite: 30,
            statut: 'soumise',
            stockReserve: true
        );

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.commandes.validate',
                    $commande->id_commande
                )
            );

        $response->assertRedirect(
            route(
                'commercial.commandes.show',
                $commande->id_commande
            )
        );

        /*
         * La commande est maintenant validée.
         */
        $this->assertDatabaseHas(
            'commandes',
            [
                'id_commande' =>
                    $commande->id_commande,

                'statut' =>
                    'validee',

                'stock_reserve' =>
                    true,

                'motif_statut' =>
                    null,
            ]
        );

        /*
         * Le stock ne doit surtout pas
         * être décrémenté une deuxième fois.
         *
         * Il reste donc à 70.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' =>
                    $offre->id_offre,

                'zone' =>
                    'Oran',

                'quantite_disponible' =>
                    70,
            ]
        );
    }


    /**
     * CAS 2
     *
     * Si une commande avec stock réservé
     * est refusée, les quantités doivent
     * être remises dans le stock.
     */
    public function test_refuser_une_commande_restitue_le_stock_reserve(): void
    {
        $commercial = $this->createCommercial();

        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $offre = $this->createOffre();

        /*
         * 30 unités avaient déjà été réservées.
         *
         * Stock initial = 100
         * Stock après réservation = 70
         */
        $this->createDisponibilite(
            $offre,
            70
        );

        $commande = $this->createCommande(
            client: $client,
            boutique: $boutique,
            offre: $offre,
            quantite: 30,
            statut: 'soumise',
            stockReserve: true
        );

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.commandes.refuse',
                    $commande->id_commande
                ),
                [
                    'motif' =>
                        'Justificatif non conforme.',
                ]
            );

        $response->assertRedirect(
            route(
                'commercial.commandes.show',
                $commande->id_commande
            )
        );

        /*
         * La commande est refusée.
         */
        $this->assertDatabaseHas(
            'commandes',
            [
                'id_commande' =>
                    $commande->id_commande,

                'statut' =>
                    'refusee',

                'motif_statut' =>
                    'Justificatif non conforme.',

                'stock_reserve' =>
                    false,
            ]
        );

        /*
         * Les 30 unités sont restituées :
         *
         * 70 + 30 = 100.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' =>
                    $offre->id_offre,

                'zone' =>
                    'Oran',

                'quantite_disponible' =>
                    100,
            ]
        );
    }


    /**
     * CAS 3
     *
     * Une commande attendait du stock.
     *
     * Au moment où le commercial essaie
     * de la valider, le stock est désormais
     * suffisant.
     *
     * Le système réserve donc le stock
     * et valide la commande.
     */
    public function test_un_commercial_peut_valider_une_commande_en_attente_si_le_stock_devient_disponible(): void
    {
        $commercial = $this->createCommercial();

        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $offre = $this->createOffre();

        /*
         * 80 unités sont maintenant disponibles.
         */
        $this->createDisponibilite(
            $offre,
            80
        );

        /*
         * La commande demandait 50 unités
         * mais elle n'avait encore rien réservé.
         */
        $commande = $this->createCommande(
            client: $client,
            boutique: $boutique,
            offre: $offre,
            quantite: 50,
            statut: 'en_attente_stock',
            stockReserve: false,
            motif: 'Disponibilité insuffisante.'
        );

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.commandes.validate',
                    $commande->id_commande
                )
            );

        $response->assertRedirect(
            route(
                'commercial.commandes.show',
                $commande->id_commande
            )
        );

        /*
         * La commande est maintenant validée
         * et le stock est marqué comme réservé.
         */
        $this->assertDatabaseHas(
            'commandes',
            [
                'id_commande' =>
                    $commande->id_commande,

                'statut' =>
                    'validee',

                'motif_statut' =>
                    null,

                'stock_reserve' =>
                    true,
            ]
        );

        /*
         * 80 - 50 = 30.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' =>
                    $offre->id_offre,

                'zone' =>
                    'Oran',

                'quantite_disponible' =>
                    30,
            ]
        );
    }


    /**
     * CAS 4
     *
     * Une commande attend du stock
     * mais la quantité est toujours insuffisante.
     *
     * Elle ne doit pas pouvoir être validée.
     */
    public function test_une_commande_reste_en_attente_si_le_stock_est_toujours_insuffisant(): void
    {
        $commercial = $this->createCommercial();

        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $offre = $this->createOffre();

        /*
         * Seulement 20 unités disponibles.
         */
        $this->createDisponibilite(
            $offre,
            20
        );

        /*
         * Le client en demande 50.
         */
        $commande = $this->createCommande(
            client: $client,
            boutique: $boutique,
            offre: $offre,
            quantite: 50,
            statut: 'en_attente_stock',
            stockReserve: false,
            motif: 'Disponibilité insuffisante.'
        );

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.commandes.validate',
                    $commande->id_commande
                )
            );

        /*
         * Le Controller intercepte l'erreur
         * métier puis redirige vers le détail.
         */
        $response->assertRedirect(
            route(
                'commercial.commandes.show',
                $commande->id_commande
            )
        );

        /*
         * Un message d'erreur doit être
         * placé dans la session.
         */
        $response->assertSessionHas(
            'error'
        );

        /*
         * La commande reste en attente.
         */
        $this->assertDatabaseHas(
            'commandes',
            [
                'id_commande' =>
                    $commande->id_commande,

                'statut' =>
                    'en_attente_stock',

                'stock_reserve' =>
                    false,
            ]
        );

        /*
         * Le stock n'a pas bougé.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' =>
                    $offre->id_offre,

                'zone' =>
                    'Oran',

                'quantite_disponible' =>
                    20,
            ]
        );
    }


    /**
     * CAS 5
     *
     * Un client ne doit pas pouvoir utiliser
     * une action réservée au commercial.
     */
    public function test_un_client_ne_peut_pas_valider_une_commande_comme_un_commercial(): void
    {
        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $offre = $this->createOffre();

        $this->createDisponibilite(
            $offre,
            70
        );

        $commande = $this->createCommande(
            client: $client,
            boutique: $boutique,
            offre: $offre,
            quantite: 30,
            statut: 'soumise',
            stockReserve: true
        );

        $response = $this
            ->actingAs($client)
            ->post(
                route(
                    'commercial.commandes.validate',
                    $commande->id_commande
                )
            );

        /*
         * Le middleware role:commercial
         * doit bloquer le client.
         */
        $response->assertStatus(403);

        /*
         * La commande n'a pas été modifiée.
         */
        $this->assertDatabaseHas(
            'commandes',
            [
                'id_commande' =>
                    $commande->id_commande,

                'statut' =>
                    'soumise',

                'stock_reserve' =>
                    true,
            ]
        );
    }
}