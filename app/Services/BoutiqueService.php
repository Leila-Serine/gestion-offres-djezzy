<?php

namespace App\Services;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BoutiqueService
{
    public function __construct(
        private DocumentService $documentService
    ) {
    }

    public function getByClient(User $client): Collection
    {
        return Boutique::where(
            'id_client',
            $client->id_user
        )->get();
    }

    /**
     * Crée une boutique avec son justificatif.
     */
    public function createForClient(
        User $client,
        array $data,
        UploadedFile $justificatifLocal
    ): Boutique {
        return DB::transaction(function () use (
            $client,
            $data,
            $justificatifLocal
        ) {
            /*
             * 1. Création de la boutique.
             */
            $boutique = Boutique::create([
                'id_client' => $client->id_user,
                'nom_boutique' => $data['nom_boutique'],
                'wilaya' => $data['wilaya'],
                'adresse' => $data['adresse'],
            ]);

            /*
             * 2. Création de son justificatif.
             */
            $this->documentService->store(
                $client,
                $justificatifLocal,
                'justificatif_local',
                $boutique
            );

            return $boutique;
        });
    }

    public function update(
        Boutique $boutique,
        array $data
    ): Boutique {
        $boutique->update($data);

        return $boutique;
    }

    public function delete(
        Boutique $boutique
    ): void {
        $boutique->delete();
    }

    public function belongsToClient(
        Boutique $boutique,
        User $client
    ): bool {
        return $boutique->id_client === $client->id_user;
    }
}