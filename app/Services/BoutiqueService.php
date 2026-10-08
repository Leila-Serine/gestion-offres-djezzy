<?php

namespace App\Services;

use App\Models\Boutique;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BoutiqueService
{
    /**
     * Retourne les boutiques appartenant à un client.
     *
     * Cette méthode existait déjà dans le projet et est utilisée
     * notamment par CommandeController.
     */
    public function getByClient(User $client): Collection
    {
        return Boutique::query()
            ->where('id_client', $client->id_user)
            ->latest('id_boutique')
            ->get();
    }

    /**
     * Crée une boutique avec son justificatif.
     */
    public function create(
        User $client,
        array $data,
        UploadedFile $justificatif
    ): Boutique {
        $path = $justificatif->store(
            'documents/boutiques',
            'local'
        );

        try {
            return DB::transaction(function () use (
                $client,
                $data,
                $path
            ) {
                $boutique = Boutique::create([
                    'id_client' => $client->id_user,
                    'nom_boutique' => $data['nom_boutique'],
                    'wilaya' => $data['wilaya'],
                    'adresse' => $data['adresse'],
                    'statut' => 'active',
                ]);

                $document = new Document();
                $document->id_user = $client->id_user;
                $document->id_boutique = $boutique->id_boutique;
                $document->type_document = 'justificatif_local';
                $document->chemin_fichier = $path;
                $document->save();

                return $boutique;
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }
    }

    /**
     * Modifie une boutique.
     *
     * Si aucun nouveau justificatif n'est envoyé,
     * l'ancien document est conservé.
     */
    public function update(
        Boutique $boutique,
        array $data,
        ?UploadedFile $nouveauJustificatif = null
    ): Boutique {
        $nouveauPath = null;

        if ($nouveauJustificatif) {
            $nouveauPath = $nouveauJustificatif->store(
                'documents/boutiques',
                'local'
            );
        }

        $ancienPath = null;

        try {
            DB::transaction(function () use (
                $boutique,
                $data,
                $nouveauPath,
                &$ancienPath
            ) {
                $boutique->update([
                    'nom_boutique' => $data['nom_boutique'],
                    'wilaya' => $data['wilaya'],
                    'adresse' => $data['adresse'],
                ]);

                if ($nouveauPath !== null) {
                    $document = $boutique
                        ->documents()
                        ->where(
                            'type_document',
                            'justificatif_local'
                        )
                        ->latest('id_document')
                        ->first();

                    if ($document) {
                        $ancienPath = $document->chemin_fichier;

                        $document->chemin_fichier = $nouveauPath;
                        $document->save();
                    } else {
                        $document = new Document();
                        $document->id_user = $boutique->id_client;
                        $document->id_boutique = $boutique->id_boutique;
                        $document->type_document = 'justificatif_local';
                        $document->chemin_fichier = $nouveauPath;
                        $document->save();
                    }
                }
            });
        } catch (Throwable $exception) {
            if ($nouveauPath !== null) {
                Storage::disk('local')->delete($nouveauPath);
            }

            throw $exception;
        }

        if (
            $ancienPath !== null
            && $ancienPath !== $nouveauPath
        ) {
            Storage::disk('local')->delete($ancienPath);
        }

        return $boutique->fresh([
            'documents',
        ]);
    }

    /**
     * Archive une boutique.
     */
    public function archiver(
        Boutique $boutique
    ): Boutique {
        $boutique->update([
            'statut' => 'archivee',
        ]);

        return $boutique->fresh();
    }

    /**
     * Réactive une boutique archivée.
     */
    public function reactiver(
        Boutique $boutique
    ): Boutique {
        $boutique->update([
            'statut' => 'active',
        ]);

        return $boutique->fresh();
    }

    /**
     * Suppression physique uniquement si la boutique
     * n'a jamais été utilisée dans une commande.
     */
    public function delete(
        Boutique $boutique
    ): bool {
        if ($boutique->commandes()->exists()) {
            return false;
        }

        $paths = $boutique
            ->documents()
            ->pluck('chemin_fichier')
            ->filter()
            ->values()
            ->all();

        DB::transaction(function () use ($boutique) {
            $boutique->documents()->delete();

            $boutique->delete();
        });

        foreach ($paths as $path) {
            Storage::disk('local')->delete($path);
        }

        return true;
    }
}