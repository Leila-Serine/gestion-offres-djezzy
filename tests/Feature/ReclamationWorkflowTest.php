<?php

namespace Tests\Feature;

use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReclamationWorkflowTest extends TestCase
{
    use RefreshDatabase;

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

    private function createCommercial(): User
    {
        return User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);
    }

    private function createReclamation(
        User $client
    ): Reclamation {
        return Reclamation::create([
            'id_client' => $client->id_user,
            'objet' => 'Problème commande',
            'message' => 'Description du problème',
            'statut' => 'ouverte',
        ]);
    }

    public function test_un_commercial_peut_repondre_a_une_reclamation(): void
    {
        $client = $this->createClient();
        $commercial = $this->createCommercial();

        $reclamation =
            $this->createReclamation($client);

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.reclamations.messages.store',
                    $reclamation->id_reclamation
                ),
                [
                    'contenu' =>
                        'Nous traitons votre demande.',
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'reclamation_messages',
            [
                'id_reclamation' =>
                    $reclamation->id_reclamation,

                'id_auteur' =>
                    $commercial->id_user,

                'contenu' =>
                    'Nous traitons votre demande.',
            ]
        );

        $this->assertDatabaseHas(
            'reclamations',
            [
                'id_reclamation' =>
                    $reclamation->id_reclamation,

                'statut' =>
                    'en_cours',
            ]
        );

        $this->assertDatabaseCount(
            'notifications',
            1
        );
    }

    public function test_le_client_peut_repondre_a_sa_reclamation(): void
    {
        $client = $this->createClient();
        $this->createCommercial();

        $reclamation =
            $this->createReclamation($client);

        $response = $this
            ->actingAs($client)
            ->post(
                route(
                    'reclamations.messages.store',
                    $reclamation->id_reclamation
                ),
                [
                    'contenu' =>
                        'Voici une précision supplémentaire.',
                ]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'reclamation_messages',
            [
                'id_reclamation' =>
                    $reclamation->id_reclamation,

                'id_auteur' =>
                    $client->id_user,
            ]
        );
    }

    public function test_un_commercial_peut_resoudre_une_reclamation(): void
    {
        $client = $this->createClient();
        $commercial = $this->createCommercial();

        $reclamation =
            $this->createReclamation($client);

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.reclamations.resolve',
                    $reclamation->id_reclamation
                )
            );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'reclamations',
            [
                'id_reclamation' =>
                    $reclamation->id_reclamation,

                'statut' =>
                    'resolue',
            ]
        );

        $this->assertDatabaseCount(
            'notifications',
            1
        );
    }

    public function test_un_client_ne_peut_pas_acceder_aux_actions_commerciales(): void
    {
        $client = $this->createClient();

        $reclamation =
            $this->createReclamation($client);

        $response = $this
            ->actingAs($client)
            ->post(
                route(
                    'commercial.reclamations.resolve',
                    $reclamation->id_reclamation
                )
            );

        $response->assertStatus(403);
    }
}