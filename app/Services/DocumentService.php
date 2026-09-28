<?php

namespace App\Services;

use App\Models\Boutique;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class DocumentService
{
    /**
     * Enregistre un document appartenant à un client.
     *
     * $boutique peut être null :
     * - null = document général du compte
     * - Boutique = document relatif à cette boutique
     */
    public function store(
        User $user,
        UploadedFile $file,
        string $typeDocument,
        ?Boutique $boutique = null
    ): Document {
        /*
         * Stockage privé du fichier.
         *
         * On n'utilise volontairement PAS le disque "public"
         * car les justificatifs sont des documents sensibles.
         */
        $path = $file->store(
            'documents',
            'local'
        );

        return Document::create([
            'id_user' => $user->id_user,

            'id_boutique' => $boutique?->id_boutique,

            'type_document' => $typeDocument,

            'chemin_fichier' => $path,
        ]);
    }
}