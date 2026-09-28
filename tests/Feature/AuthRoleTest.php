<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Petite méthode utilitaire pour créer
     * un client déjà vérifié.
     */
    private function createVerifiedClient(
        string $email = 'client@test.com'
    ): User {
        $client = User::create([
            'name' => 'Client Test',
            'email' => $email,
            'password' => 'password123',
            'role' => 'client',
        ]);

        /*
         * On utilise la méthode Laravel prévue
         * pour marquer l'adresse email comme vérifiée.
         */
        $client->markEmailAsVerified();

        return $client;
    }

    /**
     * Un utilisateur non connecté
     * ne peut pas accéder aux offres.
     */
    public function test_un_utilisateur_non_connecte_est_redirige_vers_login(): void
    {
        $response = $this->get('/offres');

        $response->assertRedirect('/login');
    }

    /**
     * Un client connecté ET vérifié
     * peut consulter la liste des offres.
     */
    public function test_un_client_peut_consulter_les_offres(): void
    {
        $client = $this->createVerifiedClient();

        $response = $this
            ->actingAs($client)
            ->get('/offres');

        $response->assertStatus(200);
    }

    /**
     * Un client ne peut pas accéder
     * au formulaire de création d'offre.
     */
    public function test_un_client_ne_peut_pas_creer_une_offre(): void
    {
        $client = $this->createVerifiedClient();

        $response = $this
            ->actingAs($client)
            ->get('/offres/create');

        $response->assertStatus(403);
    }

    /**
     * Un commercial peut accéder
     * au formulaire de création d'offre.
     */
    public function test_un_commercial_peut_acceder_a_la_creation_offre(): void
    {
        $commercial = User::create([
            'name' => 'Commercial Test',
            'email' => 'commercial@test.com',
            'password' => 'password123',
            'role' => 'commercial',
        ]);

        $response = $this
            ->actingAs($commercial)
            ->get('/offres/create');

        $response->assertStatus(200);
    }

    /**
     * Un utilisateur peut se connecter
     * avec des identifiants valides.
     */
    public function test_un_utilisateur_peut_se_connecter(): void
    {
        $user = $this->createVerifiedClient(
            'user@test.com'
        );

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/offres');

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Des identifiants incorrects sont refusés.
     */
    public function test_des_identifiants_incorrects_sont_refuses(): void
    {
        $this->createVerifiedClient(
            'user@test.com'
        );

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /**
     * Un utilisateur connecté
     * peut se déconnecter.
     */
    public function test_un_utilisateur_peut_se_deconnecter(): void
    {
        $user = $this->createVerifiedClient(
            'user@test.com'
        );

        $response = $this
            ->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/login');

        $this->assertGuest();
    }

    /**
     * Un client non vérifié
     * est redirigé vers la page de vérification.
     */
    public function test_un_client_non_verifie_est_redirige_vers_la_verification_email(): void
    {
        $client = User::create([
            'name' => 'Client Non Vérifié',
            'email' => 'nonverifie@test.com',
            'password' => 'password123',
            'role' => 'client',
        ]);

        /*
         * Ici on NE fait PAS :
         *
         * $client->markEmailAsVerified();
         *
         * car on veut justement tester
         * le comportement d'un client non vérifié.
         */

        $response = $this
            ->actingAs($client)
            ->get(route('offres.index'));

        $response->assertRedirect(
            route('verification.notice')
        );
    }
}