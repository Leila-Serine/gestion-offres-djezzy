<?php

namespace Tests\Feature;

use App\Models\Offre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OffreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que la liste des offres est accessible.
     */
    public function test_la_liste_des_offres_est_accessible(): void
    {
        $response = $this->get(route('offres.index'));

        $response->assertOk();
        $response->assertViewIs('offres.index');
        $response->assertViewHas('offres');
    }

    /**
     * Vérifie que le formulaire de création est accessible.
     */
    public function test_le_formulaire_de_creation_est_accessible(): void
    {
        $response = $this->get(route('offres.create'));

        $response->assertOk();
        $response->assertViewIs('offres.create');
    }

    /**
     * Vérifie qu'une offre peut être créée.
     */
    public function test_une_offre_peut_etre_creee(): void
    {
        $response = $this->post(route('offres.store'), [
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
     * Vérifie que les champs obligatoires sont validés.
     */
    public function test_une_offre_invalide_est_refusee(): void
    {
        $response = $this->post(route('offres.store'), [
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
     * Vérifie qu'une offre peut être modifiée.
     */
    public function test_une_offre_peut_etre_modifiee(): void
    {
        $offre = Offre::create([
            'nom_offre' => 'Djezzy Legend',
            'categorie' => 'Legend',
        ]);

        $response = $this->put(
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
     * Vérifie qu'une offre peut être supprimée.
     */
    public function test_une_offre_peut_etre_supprimee(): void
    {
        $offre = Offre::create([
            'nom_offre' => 'Djezzy Legend',
            'categorie' => 'Legend',
        ]);

        $response = $this->delete(
            route('offres.destroy', $offre->id_offre)
        );

        $response->assertRedirect(route('offres.index'));

        $this->assertDatabaseMissing('offres', [
            'id_offre' => $offre->id_offre,
        ]);
    }
}