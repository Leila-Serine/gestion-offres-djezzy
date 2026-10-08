<?php

namespace Tests\Feature;

use App\Models\Reclamation;
use App\Models\User;
use App\Services\ReclamationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReclamationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(
        string $email = 'client@test.com'
    ): User {
        return User::create([
            'name' => 'Client Test',
            'email' => $email,
            'password' => 'password123',
            'role' => 'client',
        ]);
    }


    public function test_le_service_cree_une_reclamation_pour_un_client(): void
    {
        $client = $this->createClient();

        $service = app(
            ReclamationService::class
        );

        $reclamation = $service->createForClient(
            $client,
            [
                'objet' => 'Objet test',
                'message' => 'Message test',
            ]
        );

        $this->assertDatabaseHas(
            'reclamations',
            [
                'id_reclamation' =>
                    $reclamation->id_reclamation,

                'id_client' =>
                    $client->id_user,

                'objet' =>
                    'Objet test',
            ]
        );
    }


    public function test_le_service_ne_retourne_que_les_reclamations_du_client(): void
    {
        $clientA = $this->createClient(
            'client.a@test.com'
        );

        $clientB = $this->createClient(
            'client.b@test.com'
        );

        Reclamation::create([
            'id_client' => $clientA->id_user,
            'objet' => 'Réclamation A',
            'message' => 'Message A',
        ]);

        Reclamation::create([
            'id_client' => $clientB->id_user,
            'objet' => 'Réclamation B',
            'message' => 'Message B',
        ]);

        $service = app(
            ReclamationService::class
        );

        $resultats =
            $service->getByClient($clientA);

        $this->assertCount(
            1,
            $resultats
        );

        $this->assertEquals(
            'Réclamation A',
            $resultats->first()->objet
        );
    }
}