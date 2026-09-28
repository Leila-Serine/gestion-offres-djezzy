<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {

            $table->id('id_document');

            /*
             * Le document appartient toujours
             * à un client.
             */
            $table->foreignId('id_user')
                ->constrained(
                    table: 'users',
                    column: 'id_user'
                );

            /*
             * NULL = document général du compte.
             *
             * Une valeur = document relatif
             * à une boutique particulière.
             */
            $table->foreignId('id_boutique')
                ->nullable()
                ->constrained(
                    table: 'boutiques',
                    column: 'id_boutique'
                );

            /*
             * On utilise string plutôt qu'un ENUM
             * pour pouvoir faire évoluer les types
             * sans modifier lourdement la DB.
             */
            $table->string('type_document', 50);

            /*
             * On ne stocke pas le fichier dans MySQL.
             * On stocke seulement son chemin.
             */
            $table->string('chemin_fichier', 255);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};