<?php

namespace App\Services;

use App\Models\Offre;
use Illuminate\Support\Facades\DB;

class OffreService
{
    public function create(array $data): Offre
    {
        return DB::transaction(function () use ($data) {
            $caracteristiques = $data['caracteristiques'] ?? [];

            unset($data['caracteristiques']);

            /*
             * Une nouvelle offre est active par défaut.
             */
            $data['statut'] = $data['statut'] ?? 'active';

            $offre = Offre::create($data);

            $this->syncCaracteristiques(
                $offre,
                $caracteristiques
            );

            return $offre;
        });
    }

    public function update(
        Offre $offre,
        array $data
    ): Offre {
        return DB::transaction(function () use ($offre, $data) {
            $caracteristiques = $data['caracteristiques'] ?? [];

            unset($data['caracteristiques']);

            $offre->update($data);

            $this->syncCaracteristiques(
                $offre,
                $caracteristiques
            );

            return $offre->fresh([
                'caracteristiques',
                'disponibilites',
            ]);
        });
    }

    /**
     * Suspend une offre sans supprimer son historique.
     */
    public function suspendre(Offre $offre): Offre
    {
        $offre->update([
            'statut' => 'suspendue',
        ]);

        return $offre->fresh();
    }

    /**
     * Réactive une offre suspendue.
     */
    public function reactiver(Offre $offre): Offre
    {
        $offre->update([
            'statut' => 'active',
        ]);

        return $offre->fresh();
    }

    /**
     * Supprime une offre uniquement si elle n'a jamais
     * été utilisée dans une commande.
     */
    public function delete(Offre $offre): bool
    {
        if ($offre->lignesCommande()->exists()) {
            return false;
        }

        DB::transaction(function () use ($offre) {
            /*
             * Les disponibilités régionales ne sont pas
             * historisées séparément : elles peuvent être
             * supprimées avec l'offre.
             */
            $offre->disponibilites()->delete();

            /*
             * Les caractéristiques appartiennent à l'offre.
             */
            $offre->caracteristiques()->delete();

            $offre->delete();
        });

        return true;
    }

    private function syncCaracteristiques(
        Offre $offre,
        array $caracteristiques
    ): void {
        $offre->caracteristiques()->delete();

        foreach ($caracteristiques as $index => $caracteristique) {
            $libelle = trim(
                (string) ($caracteristique['libelle'] ?? '')
            );

            $valeur = trim(
                (string) ($caracteristique['valeur'] ?? '')
            );

            $unite = trim(
                (string) ($caracteristique['unite'] ?? '')
            );

            /*
             * Une ligne incomplète est ignorée.
             */
            if ($libelle === '' || $valeur === '') {
                continue;
            }

            $offre->caracteristiques()->create([
                'libelle' => $libelle,
                'valeur' => $valeur,
                'unite' => $unite !== '' ? $unite : null,
                'ordre' => $index,
            ]);
        }
    }
}
