<?php

namespace Tests\Feature;

use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReclamationTest extends TestCase
{
    use RefreshDatabase;


    private function createVerifiedClient(
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


    public function test_un_client_peut_avoir_plusieurs_reclamations(): void
    {
        $client = $this->createVerifiedClient();

        Reclamation::create([
            'id_client' => $client->id_user,
            'objet' => 'Réclamation 1',
            'message' => 'Premier message',
        ]);

        Reclamation::create([
            'id_client' => $client->id_user,
            'objet' => 'Réclamation 2',
            'message' => 'Deuxième message',
        ]);

        $this->assertCount(
            2,
            $client->reclamations
        );
    }


    public function test_une_reclamation_appartient_a_un_client(): void
    {
        $client = $this->createVerifiedClient();

        $reclamation = Reclamation::create([
            'id_client' => $client->id_user,
            'objet' => 'Test',
            'message' => 'Message',
        ]);

        $this->assertEquals(
            $client->id_user,
            $reclamation->client->id_user
        );
    }


    public function test_un_client_ne_peut_pas_consulter_la_reclamation_d_un_autre_client(): void
    {
        $clientA = $this->createVerifiedClient(
            'client.a@test.com'
        );

        $clientB = $this->createVerifiedClient(
            'client.b@test.com'
        );

        $reclamation = Reclamation::create([
            'id_client' => $clientB->id_user,
            'objet' => 'Réclamation B',
            'message' => 'Message B',
        ]);

        $response = $this
            ->actingAs($clientA)
            ->get(
                route(
                    'reclamations.show',
                    $reclamation->id_reclamation
                )
            );

        $response->assertStatus(403);
    }

    public function test_un_client_verifie_peut_creer_une_reclamation(): void
{
    $client = $this->createVerifiedClient();

    $response = $this
        ->actingAs($client)
        ->post(route('reclamations.store'), [
            'objet' => 'Problème avec une commande',
            'message' => 'Je souhaite signaler un problème concernant ma commande.',
        ]);

    $response->assertRedirect(
        route('reclamations.index')
    );

    $this->assertDatabaseHas('reclamations', [
        'id_client' => $client->id_user,
        'objet' => 'Problème avec une commande',
        'message' => 'Je souhaite signaler un problème concernant ma commande.',
    ]);
}


public function test_une_reclamation_sans_objet_est_refusee(): void
{
    $client = $this->createVerifiedClient();

    $response = $this
        ->actingAs($client)
        ->post(route('reclamations.store'), [
            'objet' => '',
            'message' => 'Contenu de la réclamation.',
        ]);

    $response->assertSessionHasErrors('objet');

    $this->assertDatabaseCount(
        'reclamations',
        0
    );
}


public function test_une_reclamation_sans_message_est_refusee(): void
{
    $client = $this->createVerifiedClient();

    $response = $this
        ->actingAs($client)
        ->post(route('reclamations.store'), [
            'objet' => 'Réclamation test',
            'message' => '',
        ]);

    $response->assertSessionHasErrors('message');

    $this->assertDatabaseCount(
        'reclamations',
        0
    );
}

/**
 * Test that a commercial user cannot create a reclamation for a client.
 */
public function test_un_commercial_ne_peut_pas_creer_une_reclamation_client(): void
{
    $commercial = User::create([
        'name' => 'Commercial Test',
        'email' => 'commercial@test.com',
        'password' => 'password123',
        'role' => 'commercial',
    ]);

    $response = $this
        ->actingAs($commercial)
        ->post(route('reclamations.store'), [
            'objet' => 'Test',
            'message' => 'Message test',
        ]);

    $response->assertStatus(403);
}
}