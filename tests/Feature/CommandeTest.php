<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Commande;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(
    string $email = 'client@test.com'
): User {
    $client = User::create([
        'name' => 'Client Test',
        'email' => $email,
        'password' => 'password123',
        'role' => 'client',
    ]);

    // Marque réellement l'adresse email comme vérifiée.
    $client->markEmailAsVerified();

    return $client;
}

    private function createBoutique(User $client): Boutique
    {
        return Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Test',
            'wilaya' => 'Oran',
            'adresse' => 'Centre-ville',
        ]);
    }

    private function createOffre(
        string $nom = 'Offre Internet'
    ): Offre {
        return Offre::create([
            'nom_offre' => $nom,
            'categorie' => 'Internet',
        ]);
    }

    public function test_un_client_peut_consulter_ses_commandes(): void
    {
        $client = $this->createClient();

        $boutique = $this->createBoutique($client);

        Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'en_attente',
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('commandes.index'));

        $response->assertOk();
    }

    public function test_un_client_peut_creer_une_commande_avec_plusieurs_offres(): void
    {
        $client = $this->createClient();

        $boutique = $this->createBoutique($client);

        $offre1 = $this->createOffre(
            'Offre Internet 10 Go'
        );

        $offre2 = $this->createOffre(
            'Offre Voix'
        );

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

        $this->assertDatabaseHas('commandes', [
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'en_attente',
        ]);

        $commande = Commande::first();

        $this->assertDatabaseHas('ligne_commandes', [
            'id_commande' => $commande->id_commande,
            'id_offre' => $offre1->id_offre,
            'quantite' => 10,
        ]);

        $this->assertDatabaseHas('ligne_commandes', [
            'id_commande' => $commande->id_commande,
            'id_offre' => $offre2->id_offre,
            'quantite' => 5,
        ]);
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
            'statut' => 'en_attente',
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
}