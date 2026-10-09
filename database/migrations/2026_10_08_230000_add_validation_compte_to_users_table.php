<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('statut_validation_compte', 30)
                ->nullable()
                ->after('role')
                ->index();

            $table->text('motif_refus_validation')
                ->nullable()
                ->after('statut_validation_compte');

            $table->timestamp('validation_compte_at')
                ->nullable()
                ->after('motif_refus_validation');

            $table->unsignedBigInteger('validation_compte_par')
                ->nullable()
                ->after('validation_compte_at')
                ->index();
        });

        /*
         * Les comptes déjà présents dans la base correspondent aux comptes
         * de démonstration / historiques déjà utilisés avant l'ajout du
         * workflow de validation. On les conserve actifs pour ne pas casser
         * l'existant.
         *
         * Les nouveaux clients seront placés en attente automatiquement par
         * ClientAccountValidationServiceProvider.
         */
        DB::table('users')
            ->where('role', 'client')
            ->update([
                'statut_validation_compte' => 'actif',
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['statut_validation_compte']);
            $table->dropIndex(['validation_compte_par']);

            $table->dropColumn([
                'statut_validation_compte',
                'motif_refus_validation',
                'validation_compte_at',
                'validation_compte_par',
            ]);
        });
    }
};
