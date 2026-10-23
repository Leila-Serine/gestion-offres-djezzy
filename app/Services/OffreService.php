<?php

namespace App\Services;

use App\Models\Offre;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OffreService
{
    public function search(array $filters = []): LengthAwarePaginator
    {
        $query = Offre::query()
            ->with(['caracteristiques', 'disponibilites']);

        if (auth()->check() && auth()->user()->role === 'client') {
            $query->where('statut', 'active')
                ->where(function ($q) {
                    $q->whereNull('mode_stock_zero')
                        ->orWhere('mode_stock_zero', '!=', 'masquee')
                        ->orWhereHas('disponibilites', function ($stockQuery) {
                            $stockQuery->where('quantite_disponible', '>', 0);
                        });
                });
        }

        if (!empty($filters['categorie'])) {
            $query->where('categorie', $filters['categorie']);
        }

        if (($filters['prix_min'] ?? null) !== null && $filters['prix_min'] !== '') {
            $query->where('prix', '>=', (float) $filters['prix_min']);
        }

        if (($filters['prix_max'] ?? null) !== null && $filters['prix_max'] !== '') {
            $query->where('prix', '<=', (float) $filters['prix_max']);
        }

        $this->applyNumericCharacteristicFilter(
            $query,
            'internet',
            $filters['internet_min'] ?? null,
            $filters['internet_unite'] ?? null
        );

        $this->applyNumericCharacteristicFilter(
            $query,
            'duree',
            $filters['duree_min'] ?? null,
            $filters['duree_unite'] ?? null
        );

        if (!empty($filters['zone'])) {
            $query->whereHas('disponibilites', function ($stockQuery) use ($filters) {
                $stockQuery
                    ->where('zone', $filters['zone'])
                    ->where('quantite_disponible', '>', 0);
            });
        }

        match ($filters['tri'] ?? null) {
            'prix_asc' => $query->orderBy('prix'),
            'prix_desc' => $query->orderByDesc('prix'),
            'nom_asc' => $query->orderBy('nom_offre'),
            'nom_desc' => $query->orderByDesc('nom_offre'),
            default => $query->orderByDesc('id_offre'),
        };

        return $query->paginate(12)->withQueryString();
    }

    public function create(array $data): Offre
    {
        return DB::transaction(function () use ($data) {
            $offre = Offre::create([
                'nom_offre' => $data['nom_offre'],
                'categorie' => $data['categorie'],
                'prix' => $data['prix'],
                'statut' => $data['statut'] ?? 'active',
                'mode_stock_zero' => $data['mode_stock_zero'],
            ]);

            $this->syncCaracteristiques($offre, $data['caracteristiques'] ?? []);
            $this->syncDisponibilites($offre, $data['disponibilites'] ?? []);

            return $offre->load(['caracteristiques', 'disponibilites']);
        });
    }

    public function update(Offre $offre, array $data): Offre
    {
        return DB::transaction(function () use ($offre, $data) {
            $offre->update([
                'nom_offre' => $data['nom_offre'],
                'categorie' => $data['categorie'],
                'prix' => $data['prix'],
                'statut' => $data['statut'] ?? $offre->statut,
                'mode_stock_zero' => $data['mode_stock_zero'],
            ]);

            $this->syncCaracteristiques($offre, $data['caracteristiques'] ?? []);
            $this->syncDisponibilites($offre, $data['disponibilites'] ?? []);

            return $offre->load(['caracteristiques', 'disponibilites']);
        });
    }

    public function delete(Offre $offre): void
    {
        DB::transaction(function () use ($offre) {
            $offre->caracteristiques()->delete();
            $offre->disponibilites()->delete();
            $offre->delete();
        });
    }

    private function applyNumericCharacteristicFilter(
        $query,
        string $code,
        mixed $min,
        ?string $unite
    ): void {
        if ($min === null || $min === '') {
            return;
        }

        $query->whereHas('caracteristiques', function ($characteristicQuery) use ($code, $min, $unite) {
            $characteristicQuery
                ->where('code', $code)
                ->where('valeur_numerique', '>=', (float) $min);

            /*
             * Sans règle métier de conversion confirmée, on compare uniquement
             * les valeurs exprimées dans la même unité.
             */
            if ($unite !== null && $unite !== '') {
                $characteristicQuery->where('unite', $unite);
            }
        });
    }

    private function syncCaracteristiques(Offre $offre, array $rows): void
    {
        $offre->caracteristiques()->delete();

        $definitions = config('offres.caracteristiques', []);

        foreach (array_values($rows) as $index => $row) {
            $code = $row['code'] ?? null;
            $value = $row['valeur_numerique'] ?? null;
            $unit = $row['unite'] ?? null;

            if (!$code || $value === null || $value === '' || !isset($definitions[$code])) {
                continue;
            }

            $definition = $definitions[$code];
            $allowedUnits = $definition['unites'] ?? [];

            if (!$unit || !in_array($unit, $allowedUnits, true)) {
                $unit = $definition['unite_defaut'] ?? ($allowedUnits[0] ?? null);
            }

            $offre->caracteristiques()->create([
                'code' => $code,
                'libelle' => $definition['label'],
                'valeur' => (string) $value,
                'valeur_numerique' => $value,
                'unite' => $unit,
                'ordre' => $index + 1,
            ]);
        }
    }

    private function syncDisponibilites(Offre $offre, array $rows): void
    {
        $submittedZones = [];

        foreach ($rows as $row) {
            $zone = trim((string) ($row['zone'] ?? ''));
            $quantity = $row['quantite_disponible'] ?? null;

            if ($zone === '' || $quantity === null || $quantity === '') {
                continue;
            }

            $submittedZones[] = $zone;

            $offre->disponibilites()->updateOrCreate(
                ['zone' => $zone],
                ['quantite_disponible' => max(0, (int) $quantity)]
            );
        }

        if ($submittedZones === []) {
            $offre->disponibilites()->delete();
        } else {
            $offre->disponibilites()
                ->whereNotIn('zone', array_unique($submittedZones))
                ->delete();
        }
    }
}
