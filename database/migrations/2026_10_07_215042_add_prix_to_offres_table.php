<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            /*
             * Prix actuel de l'offre.
             *
             * default(0) permet de garder compatibles
             * les anciennes offres déjà présentes en base.
             */
            $table
                ->decimal('prix', 12, 2)
                ->default(0)
                ->after('categorie');
        });
    }

    public function down(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->dropColumn('prix');
        });
    }
};