<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table commandes.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {

            // Clé primaire personnalisée de la commande.
            $table->id('id_commande');

            // Client ayant créé la commande.
            // Référence users.id_user.
            $table->foreignId('id_client')
                ->constrained('users', 'id_user');

            // Boutique concernée par la commande.
            // Référence boutiques.id_boutique.
            $table->foreignId('id_boutique')
                ->constrained('boutiques', 'id_boutique');

            // Statut provisoire de la commande.
            // Valeurs prévues pour l'instant :
            // en_attente, validee, refusee.
            $table->string('statut', 30);

            // created_at et updated_at.
            $table->timestamps();
        });
    }

    /**
     * Supprime la table commandes lors d'un rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};