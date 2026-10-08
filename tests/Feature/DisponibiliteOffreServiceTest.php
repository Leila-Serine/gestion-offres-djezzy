<?php

namespace Tests\Feature;

use App\Models\DisponibiliteOffre;
use App\Models\Offre;
use App\Services\DisponibiliteOffreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisponibiliteOffreServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que le service retourne uniquement
     * les disponibilités correspondant à une zone donnée.
     */
    public function test_le_service_recupere_les_disponibilites_d_une_zone(): void
    {
        $offreA = Offre::create([
            'nom_offre' => 'Offre A',
            'categorie' => 'Test',
        ]);

        $offreB = Offre::create([
            'nom_offre' => 'Offre B',
            'categorie' => 'Test',
        ]);

        DisponibiliteOffre::create([
            'id_offre' => $offreA->id_offre,
            'zone' => 'Zone A',
            'quantite_disponible' => 100,
        ]);

        DisponibiliteOffre::create([
            'id_offre' => $offreB->id_offre,
            'zone' => 'Zone A',
            'quantite_disponible' => 50,
        ]);

        DisponibiliteOffre::create([
            'id_offre' => $offreA->id_offre,
            'zone' => 'Zone B',
            'quantite_disponible' => 30,
        ]);

        $service = app(DisponibiliteOffreService::class);

        $resultats = $service->getByZone('Zone A');

        /*
         * Deux disponibilités doivent être retournées :
         * Offre A / Zone A
         * Offre B / Zone A
         */
        $this->assertCount(2, $resultats);

        /*
         * Toutes les lignes retournées doivent
         * réellement appartenir à Zone A.
         */
        $this->assertTrue(
            $resultats->every(
                fn ($disponibilite) =>
                    $disponibilite->zone === 'Zone A'
            )
        );
    }


    /**
     * Vérifie que le service retourne UNE disponibilité
     * précise pour un couple Offre + Zone.
     *
     * Depuis l'hypothèse d'unicité :
     * une offre ne possède qu'une seule ligne
     * de disponibilité par zone.
     */
    public function test_le_service_recupere_la_disponibilite_d_une_offre_et_d_une_zone(): void
    {
        $offre = Offre::create([
            'nom_offre' => 'Offre Test',
            'categorie' => 'Test',
        ]);

        DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => 'Zone A',
            'quantite_disponible' => 100,
        ]);

        DisponibiliteOffre::create([
            'id_offre' => $offre->id_offre,
            'zone' => 'Zone B',
            'quantite_disponible' => 40,
        ]);

        $service = app(DisponibiliteOffreService::class);

        /*
         * getForOfferAndZone() ne retourne plus
         * une Collection.
         *
         * Il retourne maintenant :
         * - un objet DisponibiliteOffre
         * - ou null si aucune disponibilité n'existe.
         */
        $disponibilite = $service->getForOfferAndZone(
            $offre,
            'Zone A'
        );

        /*
         * On vérifie qu'une disponibilité
         * a bien été trouvée.
         */
        $this->assertNotNull($disponibilite);

        /*
         * On vérifie la zone.
         */
        $this->assertEquals(
            'Zone A',
            $disponibilite->zone
        );

        /*
         * On vérifie la quantité disponible.
         */
        $this->assertEquals(
            100,
            $disponibilite->quantite_disponible
        );

        /*
         * On vérifie aussi que la disponibilité
         * correspond bien à l'offre demandée.
         */
        $this->assertEquals(
            $offre->id_offre,
            $disponibilite->id_offre
        );
    }


    /**
     * Vérifie que le service retourne null
     * lorsque aucune disponibilité n'existe
     * pour le couple Offre + Zone demandé.
     */
    public function test_le_service_retourne_null_si_aucune_disponibilite_n_existe(): void
    {
        $offre = Offre::create([
            'nom_offre' => 'Offre Sans Stock',
            'categorie' => 'Test',
        ]);

        $service = app(DisponibiliteOffreService::class);

        $disponibilite = $service->getForOfferAndZone(
            $offre,
            'Zone Inexistante'
        );

        $this->assertNull($disponibilite);
    }
}