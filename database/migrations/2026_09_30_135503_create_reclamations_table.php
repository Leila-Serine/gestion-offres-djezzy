<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamations', function (Blueprint $table) {

            $table->id('id_reclamation');

            /*
             * Client ayant créé la réclamation.
             */
            $table->unsignedBigInteger('id_client');

            /*
             * Objet court permettant d'identifier
             * facilement la réclamation.
             *
             * C'est un choix technique pour le prototype,
             * pas une règle métier confirmée.
             */
            $table->string('objet', 150);

            /*
             * Contenu de la réclamation.
             */
            $table->text('message');

            $table->timestamps();

            $table
                ->foreign('id_client')
                ->references('id_user')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};