<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Offre;
use App\Services\BoutiqueService;
use App\Services\CommandeService;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function __construct(
        private CommandeService $commandeService,
        private BoutiqueService $boutiqueService
    ) {
    }

    /**
     * Affiche les commandes du client connecté.
     */
    public function index(Request $request)
    {
        $client = $request->user();

        $commandes = $this->commandeService
            ->getByClient($client);

        return view(
            'commandes.index',
            compact('commandes')
        );
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create(Request $request)
    {
        $client = $request->user();

        // Seulement les boutiques appartenant au client.
      $boutiques = $this->boutiqueService
    ->getByClient($client)
    ->where('statut', 'active')
    ->values();

$offres = Offre::query()
    ->where('statut', 'active')
    ->get();

        return view(
            'commandes.create',
            compact('boutiques', 'offres')
        );
    }

    /**
     * Enregistre une commande avec plusieurs offres.
     */
    public function store(Request $request)
    {
        $client = $request->user();

        $validatedData = $request->validate([
            'id_boutique' => [
                'required',
                'integer',
                'exists:boutiques,id_boutique',
            ],

            'lignes' => [
                'required',
                'array',
                'min:1',
            ],

            'lignes.*.id_offre' => [
                'required',
                'integer',
                'distinct',
                'exists:offres,id_offre',
            ],

            'lignes.*.quantite' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        /*
         * Sécurité importante :
         * on vérifie que la boutique choisie appartient
         * réellement au client connecté.
         */
        $boutique = $client->boutiques()
            ->where(
                'id_boutique',
                $validatedData['id_boutique']
            )
            ->first();

        if (! $boutique) {
            abort(403);
        }

        $this->commandeService->createForClient(
    $client,
    $boutique,
    $validatedData['lignes']
);

        return redirect()
            ->route('commandes.index')
            ->with(
                'success',
                'Commande créée avec succès.'
            );
    }

    /**
     * Affiche une commande du client.
     */
    public function show(
        Request $request,
        Commande $commande
    ) {
        $client = $request->user();

        if (! $this->commandeService->belongsToClient(
            $commande,
            $client
        )) {
            abort(403);
        }

        $commande->load([
            'boutique',
            'lignes.offre',
        ]);

        return view(
            'commandes.show',
            compact('commande')
        );
    }
}