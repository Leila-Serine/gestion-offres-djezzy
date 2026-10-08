<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilite_offres', function (Blueprint $table) {

            /*
             * Identifiant propre à la disponibilité.
             */
            $table->id('id_disponibilite');

            /*
             * Offre concernée.
             */
            $table->unsignedBigInteger('id_offre');

            /*
             * Zone géographique concernée.
             *
             * On utilise volontairement "zone"
             * tant que l'encadrant n'a pas confirmé
             * s'il s'agit d'une wilaya ou d'une région commerciale.
             */
            $table->string('zone', 100);

            /*
             * Nombre de puces actuellement disponibles.
             */
            $table->unsignedInteger('quantite_disponible');

            $table->timestamps();

            /*
             * Relation vers offres.
             */
            $table
                ->foreign('id_offre')
                ->references('id_offre')
                ->on('offres');

            /*
             * Index pour faciliter les recherches
             * par offre et par zone.
             *
             * Ce n'est PAS une contrainte unique :
             * on attend encore de connaître exactement
             * la granularité métier du stock.
             */
            $table->index([
                'id_offre',
                'zone',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilite_offres');
    }
};