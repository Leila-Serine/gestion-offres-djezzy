<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Services\OffreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OffreController extends Controller
{
    public function __construct(
        private OffreService $offreService
    ) {
    }

    /**
     * Affiche la liste des offres.
     *
     * - Client : uniquement les offres actives.
     * - Commercial : toutes les offres, y compris suspendues.
     */
    public function index(): View
    {
        $query = Offre::query()
            ->with('disponibilites')
            ->withCount('lignesCommande')
            ->latest('id_offre');

        if (auth()->user()->role === 'client') {
            $query->active();
        }

        $offres = $query->get();

        return view(
            'offres.index',
            compact('offres')
        );
    }

    /**
     * Affiche les détails d'une offre.
     */
    public function show(Offre $offre): View
    {
        /*
         * Un client ne doit pas pouvoir contourner le catalogue
         * en tapant directement l'URL d'une offre suspendue.
         */
        if (
            auth()->user()->role === 'client'
            && $offre->statut !== 'active'
        ) {
            abort(404);
        }

        $offre->load([
            'disponibilites',
            'caracteristiques',
        ]);

        return view(
            'offres.show',
            compact('offre')
        );
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create(): View
    {
        return view('offres.create');
    }

    /**
     * Enregistre une nouvelle offre.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'nom_offre' => [
                'required',
                'string',
                'max:100',
            ],

            'categorie' => [
                'required',
                'string',
                'max:20',
            ],

            'prix' => [
                'required',
                'numeric',
                'min:0',
            ],

            'mode_stock_zero' => [
                'required',
                'in:bientot_disponible,masquee',
            ],

            'caracteristiques' => [
                'nullable',
                'array',
            ],

            'caracteristiques.*.libelle' => [
                'nullable',
                'string',
                'max:100',
            ],

            'caracteristiques.*.valeur' => [
                'nullable',
                'string',
                'max:255',
            ],

            'caracteristiques.*.unite' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $this->offreService->create(
            $validated
        );

        return redirect()
            ->route('offres.index')
            ->with(
                'success',
                'Offre créée avec succès.'
            );
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(
        Offre $offre
    ): View {
        $offre->load('caracteristiques');

        return view(
            'offres.edit',
            compact('offre')
        );
    }

    /**
     * Met à jour une offre ou traite une action
     * de suspension / réactivation.
     */
    public function update(
        Request $request,
        Offre $offre
    ): RedirectResponse {
        /*
         * On réutilise la route offres.update existante.
         * Aucun ajout dans routes/web.php n'est nécessaire.
         */
        if ($request->input('action_statut') === 'suspendre') {
            $this->offreService->suspendre($offre);

            return redirect()
                ->route('offres.index')
                ->with(
                    'success',
                    'Offre suspendue avec succès. Elle n’est plus visible par les clients.'
                );
        }

        if ($request->input('action_statut') === 'reactiver') {
            $this->offreService->reactiver($offre);

            return redirect()
                ->route('offres.index')
                ->with(
                    'success',
                    'Offre réactivée avec succès.'
                );
        }

        $validated = $request->validate([
            'nom_offre' => [
                'required',
                'string',
                'max:100',
            ],

            'categorie' => [
                'required',
                'string',
                'max:20',
            ],

            'prix' => [
                'required',
                'numeric',
                'min:0',
            ],

            'mode_stock_zero' => [
                'required',
                'in:bientot_disponible,masquee',
            ],

            'caracteristiques' => [
                'nullable',
                'array',
            ],

            'caracteristiques.*.libelle' => [
                'nullable',
                'string',
                'max:100',
            ],

            'caracteristiques.*.valeur' => [
                'nullable',
                'string',
                'max:255',
            ],

            'caracteristiques.*.unite' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $this->offreService->update(
            $offre,
            $validated
        );

        return redirect()
            ->route('offres.index')
            ->with(
                'success',
                'Offre modifiée avec succès.'
            );
    }

    /**
     * Supprime une offre uniquement si aucune commande
     * ne l'utilise déjà.
     */
    public function destroy(
        Offre $offre
    ): RedirectResponse {
        $deleted = $this->offreService->delete(
            $offre
        );

        if (! $deleted) {
            return redirect()
                ->route('offres.index')
                ->with(
                    'error',
                    'Cette offre ne peut pas être supprimée car elle est déjà utilisée dans une commande. Vous pouvez la suspendre.'
                );
        }

        return redirect()
            ->route('offres.index')
            ->with(
                'success',
                'Offre supprimée avec succès.'
            );
    }
}
