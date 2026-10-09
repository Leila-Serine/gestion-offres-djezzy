<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\Document;
use App\Models\User;
use App\Notifications\BoutiqueValidationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BoutiqueValidationController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureCommercial($request->user());

        $statut = $request->string('statut')->toString();

        $query = Boutique::query()->with('client');

        if (in_array(
            $statut,
            ['en_attente_validation', 'validee', 'refusee'],
            true
        )) {
            $query->where(
                'statut_validation_justificatif',
                $statut
            );
        } else {
            $query->whereNotNull(
                'statut_validation_justificatif'
            );
        }

        $boutiques = $query
            ->orderByRaw(
                "CASE statut_validation_justificatif
                    WHEN 'en_attente_validation' THEN 1
                    WHEN 'refusee' THEN 2
                    WHEN 'validee' THEN 3
                    ELSE 4
                END"
            )
            ->latest('id_boutique')
            ->get();

        $compteurs = [
            'en_attente_validation' => Boutique::query()
                ->where(
                    'statut_validation_justificatif',
                    'en_attente_validation'
                )
                ->count(),

            'refusee' => Boutique::query()
                ->where(
                    'statut_validation_justificatif',
                    'refusee'
                )
                ->count(),

            'validee' => Boutique::query()
                ->where(
                    'statut_validation_justificatif',
                    'validee'
                )
                ->count(),
        ];

        return view(
            'commercial.boutique-validation.index',
            compact('boutiques', 'compteurs', 'statut')
        );
    }

    public function show(
        Request $request,
        Boutique $boutique
    ): View {
        $this->ensureCommercial($request->user());

        $boutique->load('client');

        $justificatif = $this
            ->justificatifBoutique($boutique);

        return view(
            'commercial.boutique-validation.show',
            compact('boutique', 'justificatif')
        );
    }

    public function commercialDocument(
        Request $request,
        Boutique $boutique
    ): BinaryFileResponse {
        $this->ensureCommercial($request->user());

        $document = $this
            ->justificatifBoutique($boutique);

        abort_unless($document, 404);

        return $this->openDocument($document);
    }

    public function clientDocument(
        Request $request,
        Boutique $boutique
    ): BinaryFileResponse {
        $this->ensureOwner($request, $boutique);

        $document = $this
            ->justificatifBoutique($boutique);

        abort_unless($document, 404);

        return $this->openDocument($document);
    }

    public function approve(
        Request $request,
        Boutique $boutique
    ): RedirectResponse {
        $this->ensureCommercial($request->user());

        $document = $this
            ->justificatifBoutique($boutique);

        if (! $document) {
            return back()->with(
                'error',
                'Aucun justificatif n’est associé à cette boutique.'
            );
        }

        $boutique->forceFill([
            'statut_validation_justificatif' => 'validee',
            'motif_refus_justificatif' => null,
            'validation_justificatif_at' => now(),
            'validation_justificatif_par'
                => $request->user()->id_user,
        ])->save();

        $client = $boutique->client;

        if ($client) {
            $client->notify(
                new BoutiqueValidationNotification(
                    $boutique->id_boutique,
                    'Boutique validée',
                    'Le justificatif de la boutique « '
                        . $boutique->nom_boutique
                        . ' » a été validé. Elle peut maintenant être utilisée pour passer une commande.'
                )
            );
        }

        return redirect()
            ->route(
                'commercial.boutiques.validation.show',
                $boutique
            )
            ->with(
                'success',
                'Le justificatif de la boutique a été validé.'
            );
    }

    public function reject(
        Request $request,
        Boutique $boutique
    ): RedirectResponse {
        $this->ensureCommercial($request->user());

        $document = $this
            ->justificatifBoutique($boutique);

        if (! $document) {
            return back()->with(
                'error',
                'Aucun justificatif n’est associé à cette boutique.'
            );
        }

        $validated = $request->validate([
            'motif_refus_justificatif' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $boutique->forceFill([
            'statut_validation_justificatif' => 'refusee',
            'motif_refus_justificatif'
                => $validated['motif_refus_justificatif'],
            'validation_justificatif_at' => now(),
            'validation_justificatif_par'
                => $request->user()->id_user,
        ])->save();

        $client = $boutique->client;

        if ($client) {
            $client->notify(
                new BoutiqueValidationNotification(
                    $boutique->id_boutique,
                    'Justificatif de boutique refusé',
                    'Le justificatif de la boutique « '
                        . $boutique->nom_boutique
                        . ' » n’a pas été validé. Consultez le motif et remplacez le document.'
                )
            );
        }

        return redirect()
            ->route(
                'commercial.boutiques.validation.show',
                $boutique
            )
            ->with(
                'success',
                'Le justificatif a été refusé et le motif a été enregistré.'
            );
    }

    public function replaceDocument(
        Request $request,
        Boutique $boutique
    ): RedirectResponse {
        $this->ensureOwner($request, $boutique);

        abort_unless(
            $boutique->statut_validation_justificatif === 'refusee',
            403
        );

        $request->validate([
            'justificatif_local' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $file = $request->file('justificatif_local');

        $newPath = $file->store(
            'documents/boutiques',
            'local'
        );

        $oldPath = null;

        try {
            DB::transaction(
                function () use (
                    $boutique,
                    $newPath,
                    &$oldPath
                ): void {
                    $document = $this
                        ->justificatifBoutique($boutique);

                    if ($document) {
                        $oldPath = $document->chemin_fichier;
                        $document->chemin_fichier = $newPath;
                        $document->type_document = 'justificatif_local';
                        $document->save();
                    } else {
                        $document = new Document();
                        $document->id_user = $boutique->id_client;
                        $document->id_boutique = $boutique->id_boutique;
                        $document->type_document = 'justificatif_local';
                        $document->chemin_fichier = $newPath;
                        $document->save();
                    }

                    $boutique->forceFill([
                        'statut_validation_justificatif'
                            => 'en_attente_validation',
                        'motif_refus_justificatif' => null,
                        'validation_justificatif_at' => null,
                        'validation_justificatif_par' => null,
                    ])->save();
                }
            );
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($newPath);

            throw $exception;
        }

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        $this->notifyCommercials(
            $boutique,
            'Justificatif de boutique remplacé',
            'Le client '
                . optional($boutique->client)->name
                . ' a remplacé le justificatif de la boutique « '
                . $boutique->nom_boutique
                . ' ». Le dossier est de nouveau en attente de validation.'
        );

        return redirect()
            ->route('boutiques.show', $boutique)
            ->with(
                'success',
                'Le nouveau justificatif a été envoyé. La boutique est de nouveau en attente de validation.'
            );
    }

    public function clientStatuses(
        Request $request
    ): JsonResponse {
        $user = $request->user();

        abort_unless(
            $user && $user->role === 'client',
            403
        );

        $boutiques = Boutique::query()
            ->where('id_client', $user->id_user)
            ->get([
                'id_boutique',
                'statut_validation_justificatif',
            ])
            ->mapWithKeys(
                function (Boutique $boutique): array {
                    $statut =
                        $boutique->statut_validation_justificatif;

                    return [
                        (string) $boutique->id_boutique => [
                            'statut' => $statut,
                            'autorisee' =>
                                $statut === null
                                || $statut === 'validee',
                        ],
                    ];
                }
            );

        return response()->json($boutiques);
    }

    private function justificatifBoutique(
        Boutique $boutique
    ): ?Document {
        $base = Document::query()
            ->where(
                'id_boutique',
                $boutique->id_boutique
            );

        $document = (clone $base)
            ->where(function ($query) {
                $query
                    ->where(
                        'type_document',
                        'justificatif_local'
                    )
                    ->orWhere(
                        'type_document',
                        'like',
                        '%justificatif%'
                    )
                    ->orWhere(
                        'type_document',
                        'like',
                        '%local%'
                    );
            })
            ->latest('id_document')
            ->first();

        return $document
            ?? $base
                ->latest('id_document')
                ->first();
    }

    private function openDocument(
        Document $document
    ): BinaryFileResponse {
        $disk = Storage::disk('local');

        abort_unless(
            $document->chemin_fichier
            && $disk->exists($document->chemin_fichier),
            404
        );

        return response()->file(
            $disk->path($document->chemin_fichier)
        );
    }

    private function ensureCommercial(
        ?User $user
    ): void {
        abort_unless(
            $user && $user->role === 'commercial',
            403
        );
    }

    private function ensureOwner(
        Request $request,
        Boutique $boutique
    ): void {
        $user = $request->user();

        abort_unless(
            $user
                && $user->role === 'client'
                && (int) $boutique->id_client
                    === (int) $user->id_user,
            403
        );
    }

    private function notifyCommercials(
        Boutique $boutique,
        string $titre,
        string $message
    ): void {
        User::query()
            ->where('role', 'commercial')
            ->get()
            ->each(
                fn (User $commercial) =>
                    $commercial->notify(
                        new BoutiqueValidationNotification(
                            $boutique->id_boutique,
                            $titre,
                            $message
                        )
                    )
            );
    }
}
