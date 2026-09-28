<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BoutiqueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crée rapidement un utilisateur client pour les tests.
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

    // Marque réellement l'adresse email comme vérifiée.
    $client->markEmailAsVerified();

    return $client;
}

    /**
     * Vérifie qu'un client peut avoir plusieurs boutiques.
     */
    public function test_un_client_peut_avoir_plusieurs_boutiques(): void
    {
        $client = $this->createClient();

        Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Centre',
            'wilaya' => 'Oran',
            'adresse' => 'Centre-ville',
        ]);

        Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Est',
            'wilaya' => 'Oran',
            'adresse' => 'Bir El Djir',
        ]);

        $this->assertCount(2, $client->boutiques);
    }

    /**
     * Vérifie qu'une boutique appartient à un client.
     */
    public function test_une_boutique_appartient_a_un_client(): void
    {
        $client = $this->createClient();

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Principale',
            'wilaya' => 'Alger',
            'adresse' => 'Bab Ezzouar',
        ]);

        $this->assertEquals(
            $client->id_user,
            $boutique->client->id_user
        );
    }

    /**
     * Vérifie qu'un client ne voit que ses propres boutiques.
     */
    public function test_un_client_ne_voit_que_ses_boutiques(): void
    {
        $clientA = $this->createClient('client.a@test.com');
        $clientB = $this->createClient('client.b@test.com');

        Boutique::create([
            'id_client' => $clientA->id_user,
            'nom_boutique' => 'Boutique A',
            'wilaya' => 'Oran',
            'adresse' => 'Adresse A',
        ]);

        Boutique::create([
            'id_client' => $clientB->id_user,
            'nom_boutique' => 'Boutique B',
            'wilaya' => 'Alger',
            'adresse' => 'Adresse B',
        ]);

        $response = $this
            ->actingAs($clientA)
            ->get(route('boutiques.index'));

        $response->assertOk();

        $response->assertSee('Boutique A');
        $response->assertDontSee('Boutique B');
    }

    /**
     * Vérifie qu'un client peut créer une boutique.
     */
    public function test_un_client_peut_creer_une_boutique(): void
    {
        $client = $this->createClient();
Storage::fake('local');

$document = UploadedFile::fake()->create(
    'justificatif.pdf',
    100,
    'application/pdf'
);
        $response = $this
    ->actingAs($client)
    ->post(route('boutiques.store'), [
        'nom_boutique' => 'Nouvelle Boutique',
        'wilaya' => 'Oran',
        'adresse' => 'Akid Lotfi',
        'justificatif_local' => $document,
    ]);

        $response->assertRedirect(route('boutiques.index'));

        $this->assertDatabaseHas('boutiques', [
            'id_client' => $client->id_user,
            'nom_boutique' => 'Nouvelle Boutique',
            'wilaya' => 'Oran',
            'adresse' => 'Akid Lotfi',
        ]);
    }

    /**
     * Vérifie que les champs obligatoires sont validés.
     */
    public function test_une_boutique_invalide_est_refusee(): void
    {
        $client = $this->createClient();

        $response = $this
            ->actingAs($client)
            ->post(route('boutiques.store'), [
                'nom_boutique' => '',
                'wilaya' => '',
                'adresse' => '',
            ]);

        $response->assertSessionHasErrors([
            'nom_boutique',
            'wilaya',
            'adresse',
        ]);

        $this->assertDatabaseCount('boutiques', 0);
    }

    /**
     * Vérifie qu'un client peut consulter sa propre boutique.
     */
    public function test_un_client_peut_consulter_sa_boutique(): void
    {
        $client = $this->createClient();

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Client',
            'wilaya' => 'Oran',
            'adresse' => 'Centre-ville',
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('boutiques.show', $boutique->id_boutique));

        $response->assertOk();
        $response->assertViewIs('boutiques.show');
        $response->assertSee('Boutique Client');
    }

    /**
     * Vérifie qu'un client ne peut pas consulter
     * la boutique d'un autre client.
     */
    public function test_un_client_ne_peut_pas_consulter_la_boutique_d_un_autre_client(): void
    {
        $clientA = $this->createClient('client.a@test.com');
        $clientB = $this->createClient('client.b@test.com');

        $boutiqueB = Boutique::create([
            'id_client' => $clientB->id_user,
            'nom_boutique' => 'Boutique B',
            'wilaya' => 'Alger',
            'adresse' => 'Adresse B',
        ]);

        $response = $this
            ->actingAs($clientA)
            ->get(route('boutiques.show', $boutiqueB->id_boutique));

        $response->assertStatus(403);
    }

    /**
     * Vérifie qu'un client peut modifier sa propre boutique.
     */
    public function test_un_client_peut_modifier_sa_boutique(): void
    {
        $client = $this->createClient();

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Ancien Nom',
            'wilaya' => 'Oran',
            'adresse' => 'Ancienne Adresse',
        ]);

        $response = $this
            ->actingAs($client)
            ->put(
                route('boutiques.update', $boutique->id_boutique),
                [
                    'nom_boutique' => 'Nouveau Nom',
                    'wilaya' => 'Oran',
                    'adresse' => 'Nouvelle Adresse',
                ]
            );

        $response->assertRedirect(route('boutiques.index'));

        $this->assertDatabaseHas('boutiques', [
            'id_boutique' => $boutique->id_boutique,
            'nom_boutique' => 'Nouveau Nom',
            'adresse' => 'Nouvelle Adresse',
        ]);
    }

    /**
     * Vérifie qu'un client ne peut pas modifier
     * la boutique d'un autre client.
     */
    public function test_un_client_ne_peut_pas_modifier_la_boutique_d_un_autre_client(): void
    {
        $clientA = $this->createClient('client.a@test.com');
        $clientB = $this->createClient('client.b@test.com');

        $boutiqueB = Boutique::create([
            'id_client' => $clientB->id_user,
            'nom_boutique' => 'Boutique B',
            'wilaya' => 'Alger',
            'adresse' => 'Adresse B',
        ]);

        $response = $this
            ->actingAs($clientA)
            ->put(
                route('boutiques.update', $boutiqueB->id_boutique),
                [
                    'nom_boutique' => 'Modification interdite',
                    'wilaya' => 'Oran',
                    'adresse' => 'Nouvelle adresse',
                ]
            );

        $response->assertStatus(403);

        $this->assertDatabaseHas('boutiques', [
            'id_boutique' => $boutiqueB->id_boutique,
            'nom_boutique' => 'Boutique B',
        ]);
    }

    /**
     * Vérifie qu'un client peut supprimer sa propre boutique.
     */
    public function test_un_client_peut_supprimer_sa_boutique(): void
    {
        $client = $this->createClient();

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique à supprimer',
            'wilaya' => 'Oran',
            'adresse' => 'Centre-ville',
        ]);

        $response = $this
            ->actingAs($client)
            ->delete(
                route('boutiques.destroy', $boutique->id_boutique)
            );

        $response->assertRedirect(route('boutiques.index'));

        $this->assertDatabaseMissing('boutiques', [
            'id_boutique' => $boutique->id_boutique,
        ]);
    }

    /**
     * Vérifie qu'un client ne peut pas supprimer
     * la boutique d'un autre client.
     */
    public function test_un_client_ne_peut_pas_supprimer_la_boutique_d_un_autre_client(): void
    {
        $clientA = $this->createClient('client.a@test.com');
        $clientB = $this->createClient('client.b@test.com');

        $boutiqueB = Boutique::create([
            'id_client' => $clientB->id_user,
            'nom_boutique' => 'Boutique B',
            'wilaya' => 'Alger',
            'adresse' => 'Adresse B',
        ]);

        $response = $this
            ->actingAs($clientA)
            ->delete(
                route('boutiques.destroy', $boutiqueB->id_boutique)
            );

        $response->assertStatus(403);

        $this->assertDatabaseHas('boutiques', [
            'id_boutique' => $boutiqueB->id_boutique,
        ]);
    }

    /**
     * Un commercial n'a pas accès au module boutiques
     * selon notre hypothèse de travail actuelle.
     */
    public function test_un_commercial_ne_peut_pas_acceder_aux_boutiques_du_client(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->get(route('boutiques.index'));

        $response->assertStatus(403);
    }
}