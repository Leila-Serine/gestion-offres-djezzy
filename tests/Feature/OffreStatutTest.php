<?php

namespace Tests\Feature;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffreStatutTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(): User
    {
        $client = new User();
        $client->name = 'Client Statut Test';
        $client->email = 'client.statut@test.com';
        $client->password = 'password123';
        $client->role = 'client';
        $client->email_verified_at = now();
        $client->save();

        return $client;
    }

    private function createCommercial(): User
    {
        $commercial = new User();
        $commercial->name = 'Commercial Statut Test';
        $commercial->email = 'commercial.statut@test.com';
        $commercial->password = 'password123';
        $commercial->role = 'commercial';
        $commercial->email_verified_at = now();
        $commercial->save();

        return $commercial;
    }

    private function createOffre(
        string $nom,
        string $statut = 'active'
    ): Offre {
        return Offre::create([
            'nom_offre' => $nom,
            'categorie' => 'Prépayée',
            'prix' => 2500,
            'mode_stock_zero' => 'bientot_disponible',
            'statut' => $statut,
        ]);
    }

    public function test_un_client_ne_voit_que_les_offres_actives(): void
    {
        $client = $this->createClient();

        $this->createOffre(
            'Offre Active',
            'active'
        );

        $this->createOffre(
            'Offre Suspendue',
            'suspendue'
        );

        $response = $this
            ->actingAs($client)
            ->get(route('offres.index'));

        $response->assertOk();
        $response->assertSee('Offre Active');
        $response->assertDontSee('Offre Suspendue');
    }

    public function test_un_client_ne_peut_pas_ouvrir_une_offre_suspendue(): void
    {
        $client = $this->createClient();

        $offre = $this->createOffre(
            'Offre Suspendue',
            'suspendue'
        );

        $response = $this
            ->actingAs($client)
            ->get(route('offres.show', $offre));

        $response->assertNotFound();
    }

    public function test_un_commercial_peut_suspendre_une_offre(): void
    {
        $commercial = $this->createCommercial();

        $offre = $this->createOffre(
            'Offre A',
            'active'
        );

        $response = $this
            ->actingAs($commercial)
            ->put(
                route('offres.update', $offre),
                [
                    'action_statut' => 'suspendre',
                ]
            );

        $response->assertRedirect(
            route('offres.index')
        );

        $this->assertDatabaseHas('offres', [
            'id_offre' => $offre->id_offre,
            'statut' => 'suspendue',
        ]);
    }

    public function test_un_commercial_peut_reactiver_une_offre(): void
    {
        $commercial = $this->createCommercial();

        $offre = $this->createOffre(
            'Offre B',
            'suspendue'
        );

        $response = $this
            ->actingAs($commercial)
            ->put(
                route('offres.update', $offre),
                [
                    'action_statut' => 'reactiver',
                ]
            );

        $response->assertRedirect(
            route('offres.index')
        );

        $this->assertDatabaseHas('offres', [
            'id_offre' => $offre->id_offre,
            'statut' => 'active',
        ]);
    }
}
