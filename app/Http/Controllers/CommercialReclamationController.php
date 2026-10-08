<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReclamationMessageRequest;
use App\Models\Reclamation;
use App\Services\ReclamationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class CommercialReclamationController extends Controller
{
    public function __construct(
        private ReclamationService $reclamationService
    ) {
    }

    public function index(): View
    {
        $reclamations =
            $this->reclamationService
                ->getAllForCommercial();

        return view(
            'commercial.reclamations.index',
            compact('reclamations')
        );
    }

    public function show(
        Reclamation $reclamation
    ): View {
        $reclamation->load([
            'client',
            'messages.auteur',
        ]);

        return view(
            'commercial.reclamations.show',
            compact('reclamation')
        );
    }

    public function storeMessage(
        StoreReclamationMessageRequest $request,
        Reclamation $reclamation
    ): RedirectResponse {
        try {
            $this->reclamationService
                ->addMessage(
                    $reclamation,
                    $request->user(),
                    $request->validated()['contenu']
                );

            return redirect()
                ->route(
                    'commercial.reclamations.show',
                    $reclamation->id_reclamation
                )
                ->with(
                    'success',
                    'Réponse envoyée.'
                );

        } catch (RuntimeException $exception) {
            return redirect()
                ->route(
                    'commercial.reclamations.show',
                    $reclamation->id_reclamation
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }
    }

    public function resolve(
        Reclamation $reclamation
    ): RedirectResponse {
        try {
            $this->reclamationService
                ->resolve($reclamation);

            return redirect()
                ->route(
                    'commercial.reclamations.show',
                    $reclamation->id_reclamation
                )
                ->with(
                    'success',
                    'Réclamation résolue.'
                );

        } catch (RuntimeException $exception) {
            return redirect()
                ->route(
                    'commercial.reclamations.show',
                    $reclamation->id_reclamation
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }
    }
}