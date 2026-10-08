<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caracteristique_offres', function (Blueprint $table) {
            $table->id('id_caracteristique');

            $table->unsignedBigInteger('id_offre');

            $table->string('libelle', 100);
            $table->string('valeur', 255);
            $table->string('unite', 30)->nullable();

            $table->unsignedSmallInteger('ordre')->default(0);

            $table->timestamps();

            $table->foreign('id_offre')
                ->references('id_offre')
                ->on('offres')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caracteristique_offres');
    }
};