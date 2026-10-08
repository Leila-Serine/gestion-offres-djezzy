<?php

namespace Database\Seeders;

use App\Models\Offre;
use Illuminate\Database\Seeder;

class OffresReellesSeeder extends Seeder
{
    public function run(): void
    {
        $offres = [
            [
                'nom_offre' => 'Djezzy LEGEND 3000',
                'categorie' => 'Prépayée',
                'prix' => 3000,
                'mode_stock_zero' => 'bientot_disponible',
                'caracteristiques' => [
                    ['libelle' => 'Internet', 'valeur' => '145', 'unite' => 'Go'],
                    ['libelle' => 'Appels tous réseaux', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS Djezzy', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS autres réseaux', 'valeur' => '25', 'unite' => 'SMS'],
                    ['libelle' => 'Validité', 'valeur' => '30', 'unite' => 'jours'],
                ],
            ],

            [
                'nom_offre' => 'Djezzy LEGEND 2500',
                'categorie' => 'Prépayée',
                'prix' => 2500,
                'mode_stock_zero' => 'bientot_disponible',
                'caracteristiques' => [
                    ['libelle' => 'Internet', 'valeur' => '120', 'unite' => 'Go'],
                    ['libelle' => 'Appels tous réseaux', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS Djezzy', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS autres réseaux', 'valeur' => '20', 'unite' => 'SMS'],
                    ['libelle' => 'Validité', 'valeur' => '30', 'unite' => 'jours'],
                ],
            ],

            [
                'nom_offre' => 'Djezzy LEGEND MAX 2000',
                'categorie' => 'Postpayée',
                'prix' => 2000,
                'mode_stock_zero' => 'bientot_disponible',
                'caracteristiques' => [
                    ['libelle' => 'Internet', 'valeur' => '70', 'unite' => 'Go'],
                    ['libelle' => 'Appels tous réseaux', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS Djezzy', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS autres réseaux', 'valeur' => '100', 'unite' => 'SMS'],
                    ['libelle' => 'International', 'valeur' => '200', 'unite' => 'DA'],
                    ['libelle' => 'Numéro', 'valeur' => '0770', 'unite' => null],
                    ['libelle' => 'Périodicité', 'valeur' => 'Mensuelle', 'unite' => null],
                ],
            ],

            [
                'nom_offre' => 'Djezzy LEGEND MAX 2500',
                'categorie' => 'Postpayée',
                'prix' => 2500,
                'mode_stock_zero' => 'bientot_disponible',
                'caracteristiques' => [
                    ['libelle' => 'Internet', 'valeur' => '100', 'unite' => 'Go'],
                    ['libelle' => 'Appels tous réseaux', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS Djezzy', 'valeur' => 'Illimités', 'unite' => null],
                    ['libelle' => 'SMS autres réseaux', 'valeur' => '150', 'unite' => 'SMS'],
                    ['libelle' => 'International', 'valeur' => '500', 'unite' => 'DA'],
                    ['libelle' => 'Numéro', 'valeur' => '0770', 'unite' => null],
                    ['libelle' => 'Périodicité', 'valeur' => 'Mensuelle', 'unite' => null],
                ],
            ],
        ];

        foreach ($offres as $data) {
            $caracteristiques = $data['caracteristiques'];

            unset($data['caracteristiques']);

            $offre = Offre::updateOrCreate(
                ['nom_offre' => $data['nom_offre']],
                $data
            );

            $offre->caracteristiques()->delete();

            foreach ($caracteristiques as $ordre => $caracteristique) {
                $offre->caracteristiques()->create([
                    'libelle' => $caracteristique['libelle'],
                    'valeur' => $caracteristique['valeur'],
                    'unite' => $caracteristique['unite'],
                    'ordre' => $ordre,
                ]);
            }
        }
    }
}