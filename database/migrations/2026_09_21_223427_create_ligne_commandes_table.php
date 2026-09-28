<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table ligne_commandes.
     */
    public function up(): void
    {
        Schema::create('ligne_commandes', function (Blueprint $table) {

            // Clé primaire personnalisée de la ligne de commande.
            $table->id('id_ligne_commande');

            // Commande à laquelle appartient cette ligne.
            // Référence commandes.id_commande.
            $table->foreignId('id_commande')
                ->constrained('commandes', 'id_commande');

            // Offre concernée par cette ligne.
            // Référence offres.id_offre.
            $table->foreignId('id_offre')
                ->constrained('offres', 'id_offre');

            // Quantité demandée pour cette offre.
            $table->unsignedInteger('quantite');

            // created_at et updated_at.
            $table->timestamps();
        });
    }

    /**
     * Supprime la table ligne_commandes lors d'un rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_commandes');
    }
};