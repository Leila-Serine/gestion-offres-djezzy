<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReclamationRequest;
use App\Models\Reclamation;
use App\Services\ReclamationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreReclamationMessageRequest;
use RuntimeException;

class ReclamationController extends Controller
{
    public function __construct(
        private ReclamationService $reclamationService
    ) {
    }


    public function index(): View
    {
        $client = auth()->user();

        $reclamations =
            $this->reclamationService
                ->getByClient($client);

        return view(
            'reclamations.index',
            compact('reclamations')
        );
    }


    public function create(): View
    {
        return view('reclamations.create');
    }


    public function store(
        StoreReclamationRequest $request
    ): RedirectResponse {
        $client = $request->user();

        $this->reclamationService
            ->createForClient(
                $client,
                $request->validated()
            );

        return redirect()
            ->route('reclamations.index')
            ->with(
                'success',
                'Votre réclamation a été enregistrée.'
            );
    }


    public function show(
        Reclamation $reclamation
    ): View {
        $client = auth()->user();

        if (
            ! $this->reclamationService
                ->belongsToClient(
                    $reclamation,
                    $client
                )
        ) {
            abort(403);
        }

        return view(
            'reclamations.show',
            compact('reclamation')
        );
    }

    public function storeMessage(
    StoreReclamationMessageRequest $request,
    Reclamation $reclamation
): RedirectResponse {
    $client = $request->user();

    if (
        ! $this->reclamationService
            ->belongsToClient(
                $reclamation,
                $client
            )
    ) {
        abort(403);
    }

    try {
        $this->reclamationService
            ->addMessage(
                $reclamation,
                $client,
                $request->validated()['contenu']
            );

        return redirect()
            ->route(
                'reclamations.show',
                $reclamation->id_reclamation
            )
            ->with(
                'success',
                'Message envoyé.'
            );

    } catch (RuntimeException $exception) {
        return redirect()
            ->route(
                'reclamations.show',
                $reclamation->id_reclamation
            )
            ->with(
                'error',
                $exception->getMessage()
            );
    }
}
}