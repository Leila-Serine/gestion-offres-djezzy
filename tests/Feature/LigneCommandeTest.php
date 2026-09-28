<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LigneCommandeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie qu'une commande peut contenir plusieurs lignes.
     */
    public function test_une_commande_peut_avoir_plusieurs_lignes(): void
    {
        // Création du client.
        $client = User::create([
            'name' => 'Client Test',
            'email' => 'client.lignes@test.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        // Création d'une boutique appartenant au client.
        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Test',
            'wilaya' => 'Oran',
            'adresse' => 'Centre-ville',
        ]);

        // Création d'une commande.
        $commande = Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'en_attente',
        ]);

        // Création de deux offres.
        $offre1 = Offre::create([
            'nom_offre' => 'Offre A',
            'categorie' => 'Internet',
        ]);

        $offre2 = Offre::create([
            'nom_offre' => 'Offre B',
            'categorie' => 'Voix',
        ]);

        // Première ligne de commande.
        LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_offre' => $offre1->id_offre,
            'quantite' => 10,
        ]);

        // Deuxième ligne de commande.
        LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_offre' => $offre2->id_offre,
            'quantite' => 5,
        ]);

        // Vérifie que la commande contient bien deux lignes.
        $this->assertCount(2, $commande->lignes);
    }

    /**
     * Vérifie qu'une ligne appartient à une commande
     * et référence une offre.
     */
    public function test_une_ligne_appartient_a_une_commande_et_une_offre(): void
    {
        $client = User::create([
            'name' => 'Client Relation',
            'email' => 'client.relation.ligne@test.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Relation',
            'wilaya' => 'Alger',
            'adresse' => 'Bab Ezzouar',
        ]);

        $commande = Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'en_attente',
        ]);

        $offre = Offre::create([
            'nom_offre' => 'Offre Relation',
            'categorie' => 'Data',
        ]);

        $ligne = LigneCommande::create([
            'id_commande' => $commande->id_commande,
            'id_offre' => $offre->id_offre,
            'quantite' => 20,
        ]);

        // Vérifie la relation LigneCommande -> Commande.
        $this->assertEquals(
            $commande->id_commande,
            $ligne->commande->id_commande
        );

        // Vérifie la relation LigneCommande -> Offre.
        $this->assertEquals(
            $offre->id_offre,
            $ligne->offre->id_offre
        );
    }

    /**
     * Vérifie qu'une offre peut apparaître dans plusieurs lignes de commande.
     */
    public function test_une_offre_peut_apparaitre_dans_plusieurs_lignes(): void
    {
        $client = User::create([
            'name' => 'Client Offre',
            'email' => 'client.offre@test.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Offre',
            'wilaya' => 'Oran',
            'adresse' => 'Akid Lotfi',
        ]);

        $commande1 = Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'en_attente',
        ]);

        $commande2 = Commande::create([
            'id_client' => $client->id_user,
            'id_boutique' => $boutique->id_boutique,
            'statut' => 'validee',
        ]);

        $offre = Offre::create([
            'nom_offre' => 'Offre Commune',
            'categorie' => 'Internet',
        ]);

        LigneCommande::create([
            'id_commande' => $commande1->id_commande,
            'id_offre' => $offre->id_offre,
            'quantite' => 10,
        ]);

        LigneCommande::create([
            'id_commande' => $commande2->id_commande,
            'id_offre' => $offre->id_offre,
            'quantite' => 15,
        ]);

        // Vérifie que l'offre apparaît bien dans deux lignes.
        $this->assertCount(2, $offre->lignesCommande);
    }
}