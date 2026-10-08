<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Commande;
use App\Models\DisponibiliteOffre;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crée un client avec une adresse email vérifiée.
     */
    private function createClient(
        string $email = 'client@test.com'
    ): User {
        $client = User::create([
            'name' => 'Client Test',
            'email' => $email,
            'password' => 'password123',
            'role' => 'client',
        ]);

        $client->markEmailAsVerified();

        return $client;
    }


    /**
     * Crée une boutique appartenant au client.
     *
     * Par défaut, la boutique est située à Oran.
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
        string $nom = 'Offre Internet'
    ): Offre {
        return Offre::create([
            'nom_offre' => $nom,
            'categorie' => 'Internet',
        ]);
    }


    /**
     * Crée une disponibilité pour une offre.
     *
     * HYPOTHÈSE DE PROTOTYPE :
     * la zone correspond actuellement à la wilaya
     * de la boutique.
     */
    private function createDisponibilite(
        Offre $offre,
        string $zone = 'Oran',
        int $quantite = 100
    ): DisponibiliteOffre {
        return DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => $zone,
            'quantite_disponible' => $quantite,
        ]);
    }


    public function test_un_client_peut_consulter_ses_commandes(): void
    {
        $client = $this->createClient();

        $boutique = $this->createBoutique($client);

        Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'soumise',
            'motif_statut' => null,
            'stock_reserve' => true,
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('commandes.index'));

        $response->assertOk();
    }


    public function test_un_client_peut_creer_une_commande_avec_plusieurs_offres(): void
    {
        $client = $this->createClient();

        /*
         * createBoutique() crée une boutique à Oran.
         */
        $boutique = $this->createBoutique($client);

        $offre1 = $this->createOffre(
            'Offre Internet 10 Go'
        );

        $offre2 = $this->createOffre(
            'Offre Voix'
        );

        /*
         * Les deux offres possèdent suffisamment
         * de stock dans la zone de la boutique.
         */
        $this->createDisponibilite(
            $offre1,
            'Oran',
            100
        );

        $this->createDisponibilite(
            $offre2,
            'Oran',
            100
        );

        /*
         * Le client commande :
         * - 10 unités de l'offre 1
         * - 5 unités de l'offre 2
         */
        $response = $this
            ->actingAs($client)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutique->id_boutique,

                'lignes' => [
                    [
                        'id_offre' => $offre1->id_offre,
                        'quantite' => 10,
                    ],
                    [
                        'id_offre' => $offre2->id_offre,
                        'quantite' => 5,
                    ],
                ],
            ]);

        $response->assertRedirect(
            route('commandes.index')
        );

        /*
         * Toutes les quantités étant disponibles :
         *
         * - la commande devient "soumise" ;
         * - le stock est réservé.
         */
        $this->assertDatabaseHas('commandes', [
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'soumise',
            'motif_statut' => null,
            'stock_reserve' => true,
        ]);

        $commande = Commande::query()->first();

        $this->assertNotNull($commande);

        /*
         * Vérifie la première ligne.
         */
        $this->assertDatabaseHas(
            'ligne_commandes',
            [
                'id_commande' => $commande->id_commande,
                'id_offre' => $offre1->id_offre,
                'quantite' => 10,
            ]
        );

        /*
         * Vérifie la deuxième ligne.
         */
        $this->assertDatabaseHas(
            'ligne_commandes',
            [
                'id_commande' => $commande->id_commande,
                'id_offre' => $offre2->id_offre,
                'quantite' => 5,
            ]
        );

        /*
         * Stock offre 1 :
         * 100 - 10 = 90.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' => $offre1->id_offre,
                'zone' => 'Oran',
                'quantite_disponible' => 90,
            ]
        );

        /*
         * Stock offre 2 :
         * 100 - 5 = 95.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' => $offre2->id_offre,
                'zone' => 'Oran',
                'quantite_disponible' => 95,
            ]
        );
    }


    public function test_un_client_ne_peut_pas_commander_pour_la_boutique_d_un_autre_client(): void
    {
        $clientA = $this->createClient(
            'client.a@test.com'
        );

        $clientB = $this->createClient(
            'client.b@test.com'
        );

        $boutiqueB = $this->createBoutique(
            $clientB
        );

        $offre = $this->createOffre();

        $response = $this
            ->actingAs($clientA)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutiqueB->id_boutique,

                'lignes' => [
                    [
                        'id_offre' => $offre->id_offre,
                        'quantite' => 3,
                    ],
                ],
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseCount(
            'commandes',
            0
        );
    }


    public function test_un_client_ne_peut_pas_consulter_la_commande_d_un_autre_client(): void
    {
        $clientA = $this->createClient(
            'client.a@test.com'
        );

        $clientB = $this->createClient(
            'client.b@test.com'
        );

        $boutiqueB = $this->createBoutique(
            $clientB
        );

        $commandeB = Commande::create([
            'id_client' => $clientB->id_user,
            'id_boutique' => $boutiqueB->id_boutique,
            'statut' => 'soumise',
            'motif_statut' => null,
            'stock_reserve' => true,
        ]);

        $response = $this
            ->actingAs($clientA)
            ->get(
                route(
                    'commandes.show',
                    $commandeB->id_commande
                )
            );

        $response->assertStatus(403);
    }


    public function test_une_commande_sans_offre_est_refusee(): void
    {
        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $response = $this
            ->actingAs($client)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutique->id_boutique,
                'lignes' => [],
            ]);

        $response->assertSessionHasErrors(
            'lignes'
        );

        $this->assertDatabaseCount(
            'commandes',
            0
        );
    }


    public function test_la_quantite_doit_etre_superieure_a_zero(): void
    {
        $client = $this->createClient();

        $boutique = $this->createBoutique(
            $client
        );

        $offre = $this->createOffre();

        $response = $this
            ->actingAs($client)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutique->id_boutique,

                'lignes' => [
                    [
                        'id_offre' => $offre->id_offre,
                        'quantite' => 0,
                    ],
                ],
            ]);

        $response->assertSessionHasErrors(
            'lignes.0.quantite'
        );

        $this->assertDatabaseCount(
            'commandes',
            0
        );
    }


    public function test_un_commercial_ne_peut_pas_acceder_aux_commandes_client(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->get(route('commandes.index'));

        $response->assertStatus(403);
    }


    public function test_le_stock_est_reserve_si_le_quota_demande_est_disponible(): void
    {
        $client = $this->createClient();

        /*
         * Le client possède une boutique à Alger.
         *
         * HYPOTHÈSE DE PROTOTYPE :
         * la wilaya de la boutique représente la zone
         * utilisée pour vérifier la disponibilité.
         */
        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Alger',
            'wilaya' => 'Alger',
            'adresse' => 'Alger Centre',
        ]);

        $offre = $this->createOffre(
            'Offre Test'
        );

        /*
         * 100 puces disponibles pour cette offre
         * dans la zone Alger.
         */
        $this->createDisponibilite(
            $offre,
            'Alger',
            100
        );

        /*
         * Le client demande 30 puces.
         */
        $response = $this
            ->actingAs($client)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutique->id_boutique,

                'lignes' => [
                    [
                        'id_offre' => $offre->id_offre,
                        'quantite' => 30,
                    ],
                ],
            ]);

        $response->assertRedirect(
            route('commandes.index')
        );

        /*
         * La quantité est disponible :
         * la commande est soumise et le stock est réservé.
         */
        $this->assertDatabaseHas(
            'commandes',
            [
                'id_client' => $client->id_user,
                'id_boutique' => $boutique->id_boutique,
                'statut' => 'soumise',
                'motif_statut' => null,
                'stock_reserve' => true,
            ]
        );

        /*
         * 100 - 30 = 70.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' => $offre->id_offre,
                'zone' => 'Alger',
                'quantite_disponible' => 70,
            ]
        );
    }


    public function test_une_commande_reste_en_attente_si_le_stock_est_insuffisant(): void
    {
        $client = $this->createClient();

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Oran',
            'wilaya' => 'Oran',
            'adresse' => 'Oran Centre',
        ]);

        $offre = $this->createOffre(
            'Offre Quota'
        );

        /*
         * Seulement 20 puces disponibles.
         */
        $this->createDisponibilite(
            $offre,
            'Oran',
            20
        );

        /*
         * Le client en demande 50.
         */
        $response = $this
            ->actingAs($client)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutique->id_boutique,

                'lignes' => [
                    [
                        'id_offre' => $offre->id_offre,
                        'quantite' => 50,
                    ],
                ],
            ]);

        $response->assertRedirect(
            route('commandes.index')
        );

        /*
         * La commande doit quand même exister.
         */
        $commande = Commande::query()->first();

        $this->assertNotNull($commande);

        /*
         * Elle attend une disponibilité de stock.
         */
        $this->assertEquals(
            'en_attente_stock',
            $commande->statut
        );

        /*
         * Aucun stock n'a été réservé.
         */
        $this->assertFalse(
            $commande->stock_reserve
        );

        /*
         * Une raison doit expliquer l'attente.
         */
        $this->assertNotNull(
            $commande->motif_statut
        );

        $this->assertStringContainsString(
            'Disponibilité insuffisante',
            $commande->motif_statut
        );

        /*
         * Aucun quota n'est réservé.
         * Le stock reste donc à 20.
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' => $offre->id_offre,
                'zone' => 'Oran',
                'quantite_disponible' => 20,
            ]
        );
    }


    public function test_aucun_stock_n_est_reserve_si_une_ligne_de_la_commande_est_insuffisante(): void
    {
        $client = $this->createClient();

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Alger',
            'wilaya' => 'Alger',
            'adresse' => 'Alger Centre',
        ]);

        $offreA = $this->createOffre(
            'Offre A'
        );

        $offreB = $this->createOffre(
            'Offre B'
        );

        /*
         * Offre A :
         * 100 disponibles, 20 seront demandées.
         */
        $this->createDisponibilite(
            $offreA,
            'Alger',
            100
        );

        /*
         * Offre B :
         * seulement 10 disponibles alors que
         * le client en demandera 50.
         */
        $this->createDisponibilite(
            $offreB,
            'Alger',
            10
        );

        $response = $this
            ->actingAs($client)
            ->post(route('commandes.store'), [
                'id_boutique' => $boutique->id_boutique,

                'lignes' => [
                    [
                        'id_offre' => $offreA->id_offre,
                        'quantite' => 20,
                    ],
                    [
                        'id_offre' => $offreB->id_offre,
                        'quantite' => 50,
                    ],
                ],
            ]);

        $response->assertRedirect(
            route('commandes.index')
        );

        /*
         * Offre A aurait pu être satisfaite,
         * mais l'ensemble de la commande ne peut pas l'être.
         *
         * HYPOTHÈSE DE PROTOTYPE :
         * la réservation est "tout ou rien".
         */
        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' => $offreA->id_offre,
                'zone' => 'Alger',
                'quantite_disponible' => 100,
            ]
        );

        $this->assertDatabaseHas(
            'disponibilite_offres',
            [
                'id_offre' => $offreB->id_offre,
                'zone' => 'Alger',
                'quantite_disponible' => 10,
            ]
        );

        $commande = Commande::query()->first();

        $this->assertNotNull($commande);

        /*
         * La commande attend le stock.
         */
        $this->assertEquals(
            'en_attente_stock',
            $commande->statut
        );

        /*
         * Aucune quantité n'a été réservée.
         */
        $this->assertFalse(
            $commande->stock_reserve
        );

        /*
         * Le motif doit expliquer le problème.
         */
        $this->assertNotNull(
            $commande->motif_statut
        );
    }
}