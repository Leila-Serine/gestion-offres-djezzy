<?php

namespace Tests\Feature;

use App\Models\Boutique;
use App\Models\Document;
use App\Models\User;
use App\Notifications\BoutiqueValidationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BoutiqueValidationTest extends TestCase
{
    use RefreshDatabase;

    private function client(): User
    {
        $client = new User();
        $client->name = 'Client Boutique Validation';
        $client->email = 'boutique.validation.client@test.com';
        $client->password = 'password123';
        $client->role = 'client';
        $client->email_verified_at = now();
        $client->statut_validation_compte = 'actif';
        $client->save();

        return $client;
    }

    private function commercial(): User
    {
        $commercial = new User();
        $commercial->name = 'Commercial Boutique Validation';
        $commercial->email = 'boutique.validation.commercial@test.com';
        $commercial->password = 'password123';
        $commercial->role = 'commercial';
        $commercial->email_verified_at = now();
        $commercial->save();

        return $commercial;
    }

    private function boutique(
        User $client,
        string $validation = 'en_attente_validation'
    ): Boutique {
        $boutique = Boutique::create([
            'id_client' => $client->id_user,
            'nom_boutique' => 'Boutique Validation',
            'wilaya' => 'Oran',
            'adresse' => 'Bir El Djir',
        ]);

        $boutique->forceFill([
            'statut_validation_justificatif' => $validation,
        ])->save();

        return $boutique;
    }

    private function justificatif(
        Boutique $boutique
    ): Document {
        Storage::fake('local');

        $file = UploadedFile::fake()->create(
            'justificatif.pdf',
            100,
            'application/pdf'
        );

        $path = $file->store(
            'documents/boutiques',
            'local'
        );

        $document = new Document();
        $document->id_user = $boutique->id_client;
        $document->id_boutique = $boutique->id_boutique;
        $document->type_document = 'justificatif_local';
        $document->chemin_fichier = $path;
        $document->save();

        return $document;
    }

    public function test_un_commercial_peut_valider_une_boutique(): void
    {
        Notification::fake();

        $client = $this->client();
        $commercial = $this->commercial();
        $boutique = $this->boutique($client);

        $this->justificatif($boutique);

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.boutiques.validation.approve',
                    $boutique
                )
            );

        $response->assertRedirect(
            route(
                'commercial.boutiques.validation.show',
                $boutique
            )
        );

        $this->assertDatabaseHas('boutiques', [
            'id_boutique' => $boutique->id_boutique,
            'statut_validation_justificatif' => 'validee',
        ]);

        Notification::assertSentTo(
            $client,
            BoutiqueValidationNotification::class
        );
    }

    public function test_un_commercial_peut_refuser_avec_un_motif(): void
    {
        Notification::fake();

        $client = $this->client();
        $commercial = $this->commercial();
        $boutique = $this->boutique($client);

        $this->justificatif($boutique);

        $response = $this
            ->actingAs($commercial)
            ->post(
                route(
                    'commercial.boutiques.validation.reject',
                    $boutique
                ),
                [
                    'motif_refus_justificatif'
                        => 'Document illisible.',
                ]
            );

        $response->assertRedirect(
            route(
                'commercial.boutiques.validation.show',
                $boutique
            )
        );

        $this->assertDatabaseHas('boutiques', [
            'id_boutique' => $boutique->id_boutique,
            'statut_validation_justificatif' => 'refusee',
            'motif_refus_justificatif' => 'Document illisible.',
        ]);
    }

    public function test_le_client_peut_remplacer_un_justificatif_refuse(): void
    {
        Notification::fake();
        Storage::fake('local');

        $client = $this->client();

        $boutique = $this->boutique(
            $client,
            'refusee'
        );

        $this->justificatif($boutique);

        $nouveau = UploadedFile::fake()->create(
            'nouveau.pdf',
            100,
            'application/pdf'
        );

        $response = $this
            ->actingAs($client)
            ->post(
                route(
                    'boutiques.validation.replace',
                    $boutique
                ),
                [
                    'justificatif_local' => $nouveau,
                ]
            );

        $response->assertRedirect(
            route('boutiques.show', $boutique)
        );

        $this->assertDatabaseHas('boutiques', [
            'id_boutique' => $boutique->id_boutique,
            'statut_validation_justificatif'
                => 'en_attente_validation',
            'motif_refus_justificatif' => null,
        ]);
    }

    public function test_un_client_ne_peut_pas_remplacer_le_document_d_une_autre_boutique(): void
    {
        Storage::fake('local');

        $clientA = $this->client();

        $clientB = new User();
        $clientB->name = 'Autre Client';
        $clientB->email = 'autre.client.validation@test.com';
        $clientB->password = 'password123';
        $clientB->role = 'client';
        $clientB->email_verified_at = now();
        $clientB->statut_validation_compte = 'actif';
        $clientB->save();

        $boutique = $this->boutique(
            $clientB,
            'refusee'
        );

        $file = UploadedFile::fake()->create(
            'nouveau.pdf',
            100,
            'application/pdf'
        );

        $response = $this
            ->actingAs($clientA)
            ->post(
                route(
                    'boutiques.validation.replace',
                    $boutique
                ),
                [
                    'justificatif_local' => $file,
                ]
            );

        $response->assertForbidden();
    }
}
