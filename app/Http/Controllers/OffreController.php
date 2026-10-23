<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Services\OffreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OffreController extends Controller
{
    public function __construct(
        private readonly OffreService $offreService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'categorie' => ['nullable', 'string', 'max:100'],
            'prix_min' => ['nullable', 'numeric', 'min:0'],
            'prix_max' => ['nullable', 'numeric', 'min:0'],

            'internet_min' => ['nullable', 'numeric', 'min:0'],
            'internet_unite' => ['nullable', 'string', 'max:20'],

            'duree_min' => ['nullable', 'numeric', 'min:0'],
            'duree_unite' => ['nullable', 'string', 'max:20'],

            'zone' => ['nullable', 'string', 'max:100'],
            'tri' => ['nullable', 'in:prix_asc,prix_desc,nom_asc,nom_desc'],
        ]);

        $this->validateFilterUnit('internet', $filters['internet_unite'] ?? null);
        $this->validateFilterUnit('duree', $filters['duree_unite'] ?? null);

        $offres = $this->offreService->search($filters);

        $categories = Offre::query()
            ->whereNotNull('categorie')
            ->where('categorie', '!=', '')
            ->distinct()
            ->orderBy('categorie')
            ->pluck('categorie');

        return view('offres.index', compact('offres', 'categories'));
    }

    public function create(): View
    {
        return view('offres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateOffre($request);

        $this->offreService->create($validated);

        return redirect()
            ->route('offres.index')
            ->with('success', 'Offre créée avec succès.');
    }

    public function show(Offre $offre): View
    {
        if (
            auth()->check()
            && auth()->user()->role === 'client'
            && $offre->statut !== 'active'
        ) {
            abort(404);
        }

        $offre->load(['caracteristiques', 'disponibilites']);

        return view('offres.show', compact('offre'));
    }

    public function edit(Offre $offre): View
    {
        $offre->load(['caracteristiques', 'disponibilites']);

        return view('offres.edit', compact('offre'));
    }

    public function update(Request $request, Offre $offre): RedirectResponse
    {
        if ($request->filled('action_statut')) {
            $validatedAction = $request->validate([
                'action_statut' => ['required', 'in:suspendre,reactiver'],
            ]);

            $nouveauStatut = $validatedAction['action_statut'] === 'suspendre'
                ? 'suspendue'
                : 'active';

            $offre->update([
                'statut' => $nouveauStatut,
            ]);

            $message = $nouveauStatut === 'suspendue'
                ? 'Offre suspendue avec succès.'
                : 'Offre réactivée avec succès.';

            return redirect()
                ->route('offres.index')
                ->with('success', $message);
        }

        $validated = $this->validateOffre($request);

        $this->offreService->update($offre, $validated);

        return redirect()
            ->route('offres.index')
            ->with('success', 'Offre mise à jour avec succès.');
    }

    public function destroy(Offre $offre): RedirectResponse
    {
        $this->offreService->delete($offre);

        return redirect()
            ->route('offres.index')
            ->with('success', 'Offre supprimée avec succès.');
    }

    private function validateOffre(Request $request): array
    {
        $validated = $request->validate([
            'nom_offre' => ['required', 'string', 'max:150'],
            'categorie' => ['required', 'string', 'max:100'],
            'prix' => ['required', 'numeric', 'min:0'],
            'mode_stock_zero' => [
                'required',
                'in:bientot_disponible,masquee',
            ],
            'statut' => ['nullable', 'in:active,suspendue'],

            'caracteristiques' => ['nullable', 'array'],
            'caracteristiques.*.code' => [
                'nullable',
                'string',
                'in:internet,duree,appels,sms,credit',
            ],
            'caracteristiques.*.valeur_numerique' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'caracteristiques.*.unite' => [
                'nullable',
                'string',
                'max:20',
            ],

            'disponibilites' => ['nullable', 'array'],
            'disponibilites.*.zone' => [
                'nullable',
                'string',
                'max:100',
            ],
            'disponibilites.*.quantite_disponible' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        /*
         * Vérifie le couple type/unité.
         * Exemple : Internet accepte Mo/Go/To, mais jamais Jours.
         */
        foreach ($validated['caracteristiques'] ?? [] as $index => $row) {
            $code = $row['code'] ?? null;
            $unite = $row['unite'] ?? null;

            if (!$code || !$unite) {
                continue;
            }

            $allowedUnits = config("offres.caracteristiques.$code.unites", []);

            if (!in_array($unite, $allowedUnits, true)) {
                throw ValidationException::withMessages([
                    "caracteristiques.$index.unite"
                        => "L'unité sélectionnée n'est pas valide pour ce type de caractéristique.",
                ]);
            }
        }

        return $validated;
    }

    private function validateFilterUnit(string $code, ?string $unite): void
    {
        if ($unite === null || $unite === '') {
            return;
        }

        $allowedUnits = config("offres.caracteristiques.$code.unites", []);

        if (!in_array($unite, $allowedUnits, true)) {
            throw ValidationException::withMessages([
                "{$code}_unite" => "Unité de filtre invalide.",
            ]);
        }
    }
}
