<?php

namespace App\Http\Controllers;

use App\Http\Requests\RefuseCommandeRequest;
use App\Models\Commande;
use App\Services\CommandeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class CommercialCommandeController extends Controller
{
    public function __construct(
        private CommandeService $commandeService
    ) {
    }

    /**
     * Liste des commandes côté commercial.
     */
    public function index(): View
    {
        $commandes =
            $this->commandeService
                ->getAllForCommercial();

        return view(
            'commercial.commandes.index',
            compact('commandes')
        );
    }


    /**
     * Détail d'une commande.
     */
    public function show(
        Commande $commande
    ): View {
        $commande->load([
            'client',
            'boutique',
            'lignes.offre',
        ]);

        return view(
            'commercial.commandes.show',
            compact('commande')
        );
    }


    /**
     * Validation commerciale.
     */
    public function validateCommande(
        Commande $commande
    ): RedirectResponse {
        try {

            $this->commandeService
                ->validateCommande($commande);

            return redirect()
                ->route(
                    'commercial.commandes.show',
                    $commande->id_commande
                )
                ->with(
                    'success',
                    'La commande a été validée.'
                );

        } catch (RuntimeException $exception) {

            return redirect()
                ->route(
                    'commercial.commandes.show',
                    $commande->id_commande
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }
    }


    /**
     * Refus commercial.
     */
    public function refuseCommande(
        RefuseCommandeRequest $request,
        Commande $commande
    ): RedirectResponse {
        try {

            $this->commandeService
                ->refuseCommande(
                    $commande,
                    $request->validated()['motif']
                );

            return redirect()
                ->route(
                    'commercial.commandes.show',
                    $commande->id_commande
                )
                ->with(
                    'success',
                    'La commande a été refusée.'
                );

        } catch (RuntimeException $exception) {

            return redirect()
                ->route(
                    'commercial.commandes.show',
                    $commande->id_commande
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }
    }
}