<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ligne_commandes', function (Blueprint $table) {

            /*
             * Prix copié depuis l'offre au moment
             * où la commande est créée.
             */
            $table
                ->decimal('prix_unitaire', 12, 2)
                ->default(0)
                ->after('quantite');

            /*
             * Montant de cette ligne :
             * prix_unitaire × quantite.
             */
            $table
                ->decimal('montant_ligne', 12, 2)
                ->default(0)
                ->after('prix_unitaire');
        });
    }

    public function down(): void
    {
        Schema::table('ligne_commandes', function (Blueprint $table) {
            $table->dropColumn([
                'prix_unitaire',
                'montant_ligne',
            ]);
        });
    }
};