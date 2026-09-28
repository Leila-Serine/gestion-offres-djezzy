<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table users.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            // Clé primaire personnalisée.
            $table->id('id_user');

            // Informations principales de l'utilisateur.
            $table->string('name', 100);
            $table->string('email', 255)->unique();

            // Date de vérification de l'adresse email.
            $table->timestamp('email_verified_at')->nullable();

            // Mot de passe.
            $table->string('password');

            // Rôle : commercial ou client.
            $table->string('role', 20);

            // Fonctionnalité "Se souvenir de moi".
            $table->rememberToken();

            // created_at et updated_at.
            $table->timestamps();
        });
    }

    /**
     * Supprime la table users lors d'un rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};