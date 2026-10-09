<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boutiques', function (Blueprint $table) {
            $table->string('statut_validation_justificatif', 30)
                ->nullable()
                ->after('statut')
                ->index();

            $table->text('motif_refus_justificatif')
                ->nullable()
                ->after('statut_validation_justificatif');

            $table->timestamp('validation_justificatif_at')
                ->nullable()
                ->after('motif_refus_justificatif');

            $table->unsignedBigInteger('validation_justificatif_par')
                ->nullable()
                ->after('validation_justificatif_at')
                ->index();
        });

        DB::table('boutiques')->update([
            'statut_validation_justificatif' => 'validee',
        ]);
    }

    public function down(): void
    {
        Schema::table('boutiques', function (Blueprint $table) {
            $table->dropIndex(['statut_validation_justificatif']);
            $table->dropIndex(['validation_justificatif_par']);

            $table->dropColumn([
                'statut_validation_justificatif',
                'motif_refus_justificatif',
                'validation_justificatif_at',
                'validation_justificatif_par',
            ]);
        });
    }
};
