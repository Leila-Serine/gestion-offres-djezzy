<?php

namespace Tests\Feature;

use App\Models\DisponibiliteOffre;
use App\Models\Offre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisponibiliteOffreTest extends TestCase
{
    use RefreshDatabase;

    public function test_une_offre_peut_avoir_des_disponibilites_par_zone(): void
    {
        /*
         * Création d'une offre de test.
         */
        $offre = Offre::create([
            'nom_offre' => 'Offre Test',
            'categorie' => 'Test',
        ]);

        /*
         * Disponibilité dans une première zone.
         */
        DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => 'Zone A',
            'quantite_disponible' => 100,
        ]);

        /*
         * Disponibilité dans une deuxième zone.
         */
        DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => 'Zone B',
            'quantite_disponible' => 50,
        ]);

        /*
         * Une offre possède bien deux disponibilités.
         */
        $this->assertCount(
            2,
            $offre->disponibilites
        );
    }


    public function test_une_disponibilite_appartient_a_une_offre(): void
    {
        $offre = Offre::create([
            'nom_offre' => 'Offre Test',
            'categorie' => 'Test',
        ]);

        $disponibilite = DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => 'Zone A',
            'quantite_disponible' => 100,
        ]);

        $this->assertEquals(
            $offre->id_offre,
            $disponibilite->offre->id_offre
        );
    }
}