<?php

namespace Tests\Feature;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie qu'un utilisateur connecté peut consulter
     * la liste des offres.
     */
    public function test_la_liste_des_offres_est_accessible(): void
    {
        // Un client a le droit de consulter les offres.
        $client = User::create([
    'name' => 'Client Test',
    'email' => 'client.liste@test.com',
    'password' => 'password123',
    'role' => 'client',
]);

$client->markEmailAsVerified();

        $response = $this
            ->actingAs($client)
            ->get(route('offres.index'));

        $response->assertOk();
        $response->assertViewIs('offres.index');
        $response->assertViewHas('offres');
    }

    /**
     * Vérifie que le formulaire de création
     * est accessible à un commercial.
     */
    public function test_le_formulaire_de_creation_est_accessible(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial.create@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->get(route('offres.create'));

        $response->assertOk();
        $response->assertViewIs('offres.create');
    }

    /**
     * Vérifie qu'un commercial peut créer une offre.
     */
    public function test_une_offre_peut_etre_creee(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial.store@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->post(route('offres.store'), [
                'nom_offre' => 'Djezzy Legend',
                'categorie' => 'Legend',
            ]);

        $response->assertRedirect(route('offres.index'));

        $this->assertDatabaseHas('offres', [
            'nom_offre' => 'Djezzy Legend',
            'categorie' => 'Legend',
        ]);
    }

    /**
     * Vérifie que la validation fonctionne
     * lors de la création d'une offre.
     */
    public function test_une_offre_invalide_est_refusee(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial.validation@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->post(route('offres.store'), [
                'nom_offre' => '',
                'categorie' => '',
            ]);

        $response->assertSessionHasErrors([
            'nom_offre',
            'categorie',
        ]);

        $this->assertDatabaseCount('offres', 0);
    }

    /**
     * Vérifie qu'un commercial peut modifier une offre.
     */
    public function test_une_offre_peut_etre_modifiee(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial.update@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $offre = Offre::create([
            'nom_offre' => 'Djezzy Legend',
            'categorie' => 'Legend',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->put(
                route('offres.update', $offre->id_offre),
                [
                    'nom_offre' => 'Djezzy Legend Plus',
                    'categorie' => 'Legend',
                ]
            );

        $response->assertRedirect(route('offres.index'));

        $this->assertDatabaseHas('offres', [
            'id_offre' => $offre->id_offre,
            'nom_offre' => 'Djezzy Legend Plus',
        ]);
    }

    /**
     * Vérifie qu'un commercial peut supprimer une offre.
     */
    public function test_une_offre_peut_etre_supprimee(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial.delete@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $offre = Offre::create([
            'nom_offre' => 'Djezzy Legend',
            'categorie' => 'Legend',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->delete(
                route('offres.destroy', $offre->id_offre)
            );

        $response->assertRedirect(route('offres.index'));

        $this->assertDatabaseMissing('offres', [
            'id_offre' => $offre->id_offre,
        ]);
    }
}