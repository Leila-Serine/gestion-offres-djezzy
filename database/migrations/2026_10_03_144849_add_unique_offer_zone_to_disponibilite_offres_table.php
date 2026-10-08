<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'disponibilite_offres',
            function (Blueprint $table) {

                /*
                 * HYPOTHÈSE DE PROTOTYPE :
                 *
                 * une offre possède une seule ligne
                 * de disponibilité pour une zone donnée.
                 */
                $table->unique(
                    ['id_offre', 'zone'],
                    'disponibilite_offre_zone_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'disponibilite_offres',
            function (Blueprint $table) {

                $table->dropUnique(
                    'disponibilite_offre_zone_unique'
                );
            }
        );
    }
};