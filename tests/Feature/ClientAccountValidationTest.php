<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use App\Notifications\ClientValidationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientAccountValidationTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(
        string $statut = 'en_attente_validation'
    ): User {
        $client = new User();
        $client->name = 'Client Validation Test';
        $client->email =
            'client.validation@test.com';
        $client->password = 'password123';
        $client->role = 'client';
        $client->email_verified_at = now();
        $client->statut_validation_compte =
            $statut;
        $client->save();

        return $client;
    }

    private function createCommercial(): User
    {
        $commercial = new User();
        $commercial->name =
            'Commercial Validation Test';
        $commercial->email =
            'commercial.validation@test.com';
        $commercial->password = 'password123';
        $commercial->role = 'commercial';
        $commercial->email_verified_at = now();
        $commercial->save();

        return $commercial;
    }

    private function addRegistre(
        User $client
    ): Document {
        Storage::fake('local');

        $file = UploadedFile::fake()->create(
            'registre.pdf',
            100,
            'application/pdf'
        );

        $path = $file->store(
            'documents/clients',
            'local'
        );

        $document = new Document();
        $document->id_user = $client->id_user;
        $document->id_boutique = null;
        $document->type_document =
            'registre_commerce';
        $document->chemin_fichier = $path;
        $document->save();

        return $document;
    }

    public function test_un_client_en_attente_est_bloque_apres_verification_email(): void
    {
        $client = $this->createClient(
            'en_attente_validation'
        );

        $response = $this
            ->actingAs($client)
            ->get(route('offres.index'));

        $response->assertRedirect(
            route('client.validation.status')
        );
    }

    public function test_un_client_actif_peut_acceder_aux_offres(): void
    {
        $client = $this->createClient(
            'actif'
        );

        $response = $this
            ->actingAs($client)
            ->get(route('offres.index'));

        $response->assertOk();
    }

    public function test_un_commercial_peut_valider_un_client(): void
    {
        Notification::fake();

        $client = $this->createClient();
        $commercial = $this->createCommercial();

        $this->addRegistre($client);

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.clients.validation.approve',
                    $client
                )
            );

        $response->assertRedirect(
            route(
                'commercial.clients.validation.show',
                $client
            )
        );

        $this->assertDatabaseHas('users', [
            'id_user' => $client->id_user,
            'statut_validation_compte'
                => 'actif',
        ]);

        Notification::assertSentTo(
            $client,
            ClientValidationNotification::class
        );
    }

    public function test_un_commercial_peut_refuser_avec_un_motif(): void
    {
        Notification::fake();

        $client = $this->createClient();
        $commercial = $this->createCommercial();

        $this->addRegistre($client);

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.clients.validation.reject',
                    $client
                ),
                [
                    'motif_refus_validation'
                        => 'Document illisible.',
                ]
            );

        $response->assertRedirect(
            route(
                'commercial.clients.validation.show',
                $client
            )
        );

        $this->assertDatabaseHas('users', [
            'id_user' => $client->id_user,
            'statut_validation_compte'
                => 'refuse',
            'motif_refus_validation'
                => 'Document illisible.',
        ]);
    }

    public function test_un_client_refuse_peut_remplacer_son_registre(): void
    {
        Storage::fake('local');

        $client = $this->createClient(
            'refuse'
        );

        $ancien = UploadedFile::fake()
            ->create(
                'ancien.pdf',
                100,
                'application/pdf'
            );

        $ancienPath = $ancien->store(
            'documents/clients',
            'local'
        );

        $document = new Document();
        $document->id_user = $client->id_user;
        $document->id_boutique = null;
        $document->type_document =
            'registre_commerce';
        $document->chemin_fichier =
            $ancienPath;
        $document->save();

        $nouveau = UploadedFile::fake()
            ->create(
                'nouveau.pdf',
                100,
                'application/pdf'
            );

        $response = $this
            ->actingAs($client)
            ->post(
                route(
                    'client.validation.replace-document'
                ),
                [
                    'registre_commerce'
                        => $nouveau,
                ]
            );

        $response->assertRedirect(
            route('client.validation.status')
        );

        $this->assertDatabaseHas('users', [
            'id_user' => $client->id_user,
            'statut_validation_compte'
                => 'en_attente_validation',
            'motif_refus_validation' => null,
        ]);
    }
}
