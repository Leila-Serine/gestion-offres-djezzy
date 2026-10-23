<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caracteristique_offres', function (Blueprint $table) {
            if (!Schema::hasColumn('caracteristique_offres', 'code')) {
                $table->string('code', 30)->nullable()->after('id_offre');
            }

            if (!Schema::hasColumn('caracteristique_offres', 'valeur_numerique')) {
                $table->decimal('valeur_numerique', 12, 2)->nullable()->after('valeur');
            }
        });

        /*
         * Migration douce des anciennes caractéristiques.
         * On conserve libelle / valeur / unite pour ne rien casser,
         * mais on remplit les nouveaux champs normalisés lorsque possible.
         */
        $idColumn = Schema::hasColumn('caracteristique_offres', 'id_caracteristique')
            ? 'id_caracteristique'
            : (Schema::hasColumn('caracteristique_offres', 'id') ? 'id' : null);

        if ($idColumn !== null) {
            foreach (DB::table('caracteristique_offres')->get() as $row) {
                $normalizedLabel = Str::lower(Str::ascii((string) ($row->libelle ?? '')));

                $code = match (true) {
                    str_contains($normalizedLabel, 'internet'),
                    str_contains($normalizedLabel, 'data'),
                    str_contains($normalizedLabel, 'go') => 'internet',

                    str_contains($normalizedLabel, 'duree'),
                    str_contains($normalizedLabel, 'validite'),
                    str_contains($normalizedLabel, 'jour') => 'duree',

                    str_contains($normalizedLabel, 'appel'),
                    str_contains($normalizedLabel, 'minute') => 'appels',

                    str_contains($normalizedLabel, 'sms') => 'sms',

                    str_contains($normalizedLabel, 'credit') => 'credit',

                    default => null,
                };

                $unit = match ($code) {
                    'internet' => 'Go',
                    'duree' => 'Jours',
                    'appels' => 'Minutes',
                    'sms' => 'SMS',
                    'credit' => 'DA',
                    default => $row->unite ?? null,
                };

                $rawValue = str_replace(',', '.', (string) ($row->valeur ?? ''));
                preg_match('/-?\d+(?:\.\d+)?/', $rawValue, $matches);
                $numericValue = isset($matches[0]) ? (float) $matches[0] : null;

                DB::table('caracteristique_offres')
                    ->where($idColumn, $row->{$idColumn})
                    ->update([
                        'code' => $code,
                        'valeur_numerique' => $numericValue,
                        'unite' => $unit,
                    ]);
            }
        }

        Schema::table('caracteristique_offres', function (Blueprint $table) {
            // Les index accélèrent les filtres code + valeur.
            try {
                $table->index(['code', 'valeur_numerique'], 'carac_code_valeur_idx');
            } catch (\Throwable $e) {
                // Index déjà présent : aucune action.
            }
        });
    }

    public function down(): void
    {
        Schema::table('caracteristique_offres', function (Blueprint $table) {
            try {
                $table->dropIndex('carac_code_valeur_idx');
            } catch (\Throwable $e) {
                // Ignore si l'index n'existe pas.
            }

            $columns = [];

            if (Schema::hasColumn('caracteristique_offres', 'code')) {
                $columns[] = 'code';
            }

            if (Schema::hasColumn('caracteristique_offres', 'valeur_numerique')) {
                $columns[] = 'valeur_numerique';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
