<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Applique la migration.
     */
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id('id_offre');
            $table->string('nom_offre', 100);
            $table->string('categorie', 20);
            $table->timestamps();
        });
    }

    /**
     * Annule la migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};