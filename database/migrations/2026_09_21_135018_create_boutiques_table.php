<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table boutiques.
     */
    public function up(): void
    {
        Schema::create('boutiques', function (Blueprint $table) {

            // Clé primaire personnalisée de la boutique.
            $table->id('id_boutique');

            // Client propriétaire de la boutique.
            // Référence users.id_user.
            $table->foreignId('id_client')
                ->constrained('users', 'id_user');

            // Nom de la boutique.
            $table->string('nom_boutique', 100);

            // Wilaya dans laquelle se trouve la boutique.
            $table->string('wilaya', 50);

            // Adresse de la boutique.
            $table->string('adresse', 255);

            // created_at et updated_at.
            $table->timestamps();
        });
    }

    /**
     * Supprime la table boutiques lors d'un rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('boutiques');
    }
};