<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Services\BoutiqueService;
use Illuminate\Http\Request;

class BoutiqueController extends Controller
{
    /**
     * Injection du service BoutiqueService.
     */
    public function __construct(
        private BoutiqueService $boutiqueService
    ) {
    }

    /**
     * Affiche les boutiques du client connecté.
     */
    public function index(Request $request)
    {
        $client = $request->user();

        $boutiques = $this->boutiqueService->getByClient($client);

        return view('boutiques.index', compact('boutiques'));
    }

    /**
     * Affiche le formulaire de création d'une boutique.
     */
    public function create()
    {
        return view('boutiques.create');
    }

    /**
     * Enregistre une nouvelle boutique.
     */
   public function store(Request $request)
{
    $client = $request->user();

    $validatedData = $request->validate([
        'nom_boutique' => [
            'required',
            'string',
            'max:100',
        ],

        'wilaya' => [
            'required',
            'string',
            'max:50',
        ],

        'adresse' => [
            'required',
            'string',
            'max:255',
        ],

        'justificatif_local' => [
            'required',
            'file',
            'mimes:pdf,jpg,jpeg,png',
            'max:5120',
        ],
    ]);

    $this->boutiqueService->createForClient(
        $client,
        $validatedData,
        $request->file('justificatif_local')
    );

    return redirect()
        ->route('boutiques.index')
        ->with(
            'success',
            'Boutique créée avec succès.'
        );
}

    /**
     * Affiche le détail d'une boutique.
     */
    public function show(Request $request, Boutique $boutique)
    {
        $client = $request->user();

        $this->ensureOwnership($boutique, $client);

        return view(
            'boutiques.show',
            compact('boutique')
        );
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Request $request, Boutique $boutique)
    {
        $client = $request->user();

        $this->ensureOwnership($boutique, $client);

        return view(
            'boutiques.edit',
            compact('boutique')
        );
    }

    /**
     * Met à jour une boutique.
     */
    public function update(
        Request $request,
        Boutique $boutique
    ) {
        $client = $request->user();

        $this->ensureOwnership($boutique, $client);

        $validatedData = $request->validate([
            'nom_boutique' => ['required', 'string', 'max:100'],
            'wilaya' => ['required', 'string', 'max:50'],
            'adresse' => ['required', 'string', 'max:255'],
        ]);

        $this->boutiqueService->update(
            $boutique,
            $validatedData
        );

        return redirect()
            ->route('boutiques.index')
            ->with('success', 'Boutique modifiée avec succès.');
    }

    /**
     * Supprime une boutique.
     */
    public function destroy(
        Request $request,
        Boutique $boutique
    ) {
        $client = $request->user();

        $this->ensureOwnership($boutique, $client);

        $this->boutiqueService->delete($boutique);

        return redirect()
            ->route('boutiques.index')
            ->with('success', 'Boutique supprimée avec succès.');
    }

    /**
     * Vérifie que la boutique appartient
     * bien au client connecté.
     */
    private function ensureOwnership(
        Boutique $boutique,
        $client
    ): void {
        if (! $this->boutiqueService->belongsToClient(
            $boutique,
            $client
        )) {
            abort(403);
        }
    }
}