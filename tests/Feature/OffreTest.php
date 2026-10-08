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
     * Crée un client vérifié pour les tests.
     *
     * On affecte les propriétés une par une pour éviter
     * les restrictions de mass assignment ($fillable).
     */
    private function createClient(): User
    {
        $client = new User();

        $client->name = 'Client Test Offre';
        $client->email = 'client.offres@test.com';
        $client->password = 'password123';
        $client->role = 'client';
        $client->email_verified_at = now();

        $client->save();

        return $client;
    }

    /**
     * Crée un commercial vérifié pour les tests.
     */
    private function createCommercial(): User
    {
        $commercial = new User();

        $commercial->name = 'Commercial Test Offre';
        $commercial->email = 'commercial.offres@test.com';
        $commercial->password = 'password123';
        $commercial->role = 'commercial';
        $commercial->email_verified_at = now();

        $commercial->save();

        return $commercial;
    }

    /**
     * Vérifie qu'un client peut consulter
     * la liste des offres.
     */
    public function test_la_liste_des_offres_est_accessible(): void
    {
        $client = $this->createClient();

        Offre::create([
            'nom_offre' => 'Offre Internet',
            'categorie' => 'Internet',
            'prix' => 1000,
            'mode_stock_zero' => 'bientot_disponible',
        ]);

        $response = $this
            ->actingAs($client)
            ->get(route('offres.index'));

        $response->assertOk();

        $response->assertSee('Offre Internet');
    }

    /**
     * Vérifie que le commercial peut accéder
     * au formulaire de création d'une offre.
     */
    public function test_le_formulaire_de_creation_est_accessible(): void
    {
        $commercial = $this->createCommercial();

        $response = $this
            ->actingAs($commercial)
            ->get(route('offres.create'));

        $response->assertOk();
    }

    /**
     * Vérifie qu'un commercial peut créer une offre.
     */
    public function test_une_offre_peut_etre_creee(): void
    {
        $commercial = $this->createCommercial();

        $response = $this
            ->actingAs($commercial)
            ->post(
                route('offres.store'),
                [
                    'nom_offre' => 'Djezzy Legend',
                    'categorie' => 'Legend',

                    // Valeur fictive uniquement utilisée
                    // pour le test automatisé.
                    'prix' => 1500,

                    'mode_stock_zero' => 'bientot_disponible',
                ]
            );

        $response->assertRedirect(
            route('offres.index')
        );

        $this->assertDatabaseHas(
            'offres',
            [
                'nom_offre' => 'Djezzy Legend',
                'categorie' => 'Legend',
                'prix' => 1500,
                'mode_stock_zero' => 'bientot_disponible',
            ]
        );
    }

    /**
     * Vérifie que les champs obligatoires
     * sont correctement validés.
     */
    public function test_une_offre_invalide_est_refusee(): void
    {
        $commercial = $this->createCommercial();

        $response = $this
            ->actingAs($commercial)
            ->post(
                route('offres.store'),
                [
                    'nom_offre' => '',
                    'categorie' => '',
                    'prix' => '',
                    'mode_stock_zero' => '',
                ]
            );

        $response->assertSessionHasErrors([
            'nom_offre',
            'categorie',
            'prix',
            'mode_stock_zero',
        ]);

        $this->assertDatabaseCount(
            'offres',
            0
        );
    }

    /**
     * Vérifie qu'un commercial peut modifier une offre.
     *
     * On teste également :
     * - la modification du prix ;
     * - la configuration du comportement à stock zéro.
     */
    public function test_une_offre_peut_etre_modifiee(): void
    {
        $commercial = $this->createCommercial();

        $offre = Offre::create([
            'nom_offre' => 'Djezzy Legend',
            'categorie' => 'Legend',
            'prix' => 1500,
            'mode_stock_zero' => 'bientot_disponible',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->put(
                route(
                    'offres.update',
                    $offre
                ),
                [
                    'nom_offre' => 'Djezzy Legend Plus',
                    'categorie' => 'Legend',

                    // Valeur fictive uniquement utilisée
                    // pour le test automatisé.
                    'prix' => 1800,

                    'mode_stock_zero' => 'masquee',
                ]
            );

        $response->assertRedirect(
            route('offres.index')
        );

        $this->assertDatabaseHas(
            'offres',
            [
                'id_offre' => $offre->id_offre,
                'nom_offre' => 'Djezzy Legend Plus',
                'categorie' => 'Legend',
                'prix' => 1800,
                'mode_stock_zero' => 'masquee',
            ]
        );
    }

    /**
     * Vérifie qu'un commercial peut supprimer une offre.
     */
    public function test_une_offre_peut_etre_supprimee(): void
    {
        $commercial = $this->createCommercial();

        $offre = Offre::create([
            'nom_offre' => 'Offre à supprimer',
            'categorie' => 'Internet',
            'prix' => 500,
            'mode_stock_zero' => 'bientot_disponible',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->delete(
                route(
                    'offres.destroy',
                    $offre
                )
            );

        $response->assertRedirect(
            route('offres.index')
        );

        $this->assertDatabaseMissing(
            'offres',
            [
                'id_offre' => $offre->id_offre,
            ]
        );
    }
}