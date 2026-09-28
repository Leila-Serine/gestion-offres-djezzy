<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    /**
     * DocumentService est injecté automatiquement
     * par Laravel.
     */
    public function __construct(
        private DocumentService $documentService
    ) {
    }

    /**
     * Crée un client avec son registre de commerce.
     */
    public function register(
        array $data,
        UploadedFile $registreCommerce
    ): User {
        return DB::transaction(function () use (
            $data,
            $registreCommerce
        ) {
            /*
             * 1. Création du compte.
             *
             * L'inscription publique crée uniquement
             * un CLIENT.
             */
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'client',
            ]);

            /*
             * 2. Création du document associé au compte.
             *
             * Aucune boutique n'est passée ici :
             * id_boutique sera donc NULL.
             */
            $this->documentService->store(
                $user,
                $registreCommerce,
                'registre_commerce'
            );

            return $user;
        });
    }
}