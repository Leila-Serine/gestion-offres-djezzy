<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Notifications\ClientValidationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ClientValidationController extends Controller
{
    /**
     * Page affichée au client après vérification de l'e-mail
     * tant que le registre de commerce n'est pas validé.
     */
    public function status(
        Request $request
    ): View|RedirectResponse {
        $client = $request->user();

        $this->ensureClient($client);

        if ($client->statut_validation_compte === 'actif') {
            return redirect()->route('offres.index');
        }

        $registre = $this->registreCommerce($client);

        return view(
            'client-validation.status',
            compact(
                'client',
                'registre'
            )
        );
    }

    /**
     * Permet au client de consulter son registre de commerce.
     */
    public function clientRegistre(
        Request $request
    ): BinaryFileResponse {
        $client = $request->user();

        $this->ensureClient($client);

        $registre = $this->registreCommerce($client);

        abort_unless($registre, 404);

        return $this->openDocument($registre);
    }

    /**
     * Après refus, le client peut remplacer le document.
     * Le dossier repasse automatiquement en attente.
     */
    public function replaceDocument(
        Request $request
    ): RedirectResponse {
        $client = $request->user();

        $this->ensureClient($client);

        abort_unless(
            $client->statut_validation_compte === 'refuse',
            403
        );

        $validated = $request->validate([
            'registre_commerce' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $fichier = $request->file('registre_commerce');

        $nouveauPath = $fichier->store(
            'documents/clients',
            'local'
        );

        $ancienPath = null;

        try {
            DB::transaction(function () use (
                $client,
                $nouveauPath,
                &$ancienPath
            ) {
                $registre = $this->registreCommerce($client);

                if ($registre) {
                    $ancienPath = $registre->chemin_fichier;
                    $registre->chemin_fichier = $nouveauPath;

                    /*
                     * On normalise le type tout en conservant
                     * la même ligne de document.
                     */
                    $registre->type_document = 'registre_commerce';
                    $registre->save();
                } else {
                    $registre = new Document();
                    $registre->id_user = $client->id_user;
                    $registre->id_boutique = null;
                    $registre->type_document = 'registre_commerce';
                    $registre->chemin_fichier = $nouveauPath;
                    $registre->save();
                }

                $client->forceFill([
                    'statut_validation_compte' => 'en_attente_validation',
                    'motif_refus_validation' => null,
                    'validation_compte_at' => null,
                    'validation_compte_par' => null,
                ])->save();
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($nouveauPath);
            throw $exception;
        }

        if (
            $ancienPath
            && $ancienPath !== $nouveauPath
        ) {
            Storage::disk('local')->delete($ancienPath);
        }

        $this->notifyCommercials(
            $client,
            'Registre de commerce remplacé',
            'Le client ' . $client->name
                . ' a remplacé son registre de commerce. '
                . 'Le dossier est de nouveau en attente de validation.'
        );

        return redirect()
            ->route('client.validation.status')
            ->with(
                'success',
                'Votre nouveau registre de commerce a été envoyé. Votre dossier est de nouveau en attente de validation.'
            );
    }

    /**
     * Liste commerciale des comptes clients.
     */
    public function commercialIndex(
        Request $request
    ): View {
        $this->ensureCommercial(
            $request->user()
        );

        $statut = $request->string('statut')->toString();

        $query = User::query()
            ->where('role', 'client')
            ->whereNotNull('email_verified_at');

        if (in_array(
            $statut,
            [
                'en_attente_validation',
                'actif',
                'refuse',
            ],
            true
        )) {
            $query->where(
                'statut_validation_compte',
                $statut
            );
        }

        $clients = $query
            ->orderByRaw(
                "CASE statut_validation_compte
                    WHEN 'en_attente_validation' THEN 1
                    WHEN 'refuse' THEN 2
                    WHEN 'actif' THEN 3
                    ELSE 4
                END"
            )
            ->latest('id_user')
            ->get();

        $compteurs = [
            'en_attente_validation' => User::query()
                ->where('role', 'client')
                ->whereNotNull('email_verified_at')
                ->where(
                    'statut_validation_compte',
                    'en_attente_validation'
                )
                ->count(),

            'refuse' => User::query()
                ->where('role', 'client')
                ->whereNotNull('email_verified_at')
                ->where(
                    'statut_validation_compte',
                    'refuse'
                )
                ->count(),

            'actif' => User::query()
                ->where('role', 'client')
                ->whereNotNull('email_verified_at')
                ->where(
                    'statut_validation_compte',
                    'actif'
                )
                ->count(),
        ];

        return view(
            'commercial.client-validation.index',
            compact(
                'clients',
                'compteurs',
                'statut'
            )
        );
    }

    /**
     * Détail d'un dossier client.
     */
    public function commercialShow(
        Request $request,
        User $client
    ): View {
        $this->ensureCommercial(
            $request->user()
        );

        abort_unless(
            $client->role === 'client',
            404
        );

        $registre = $this->registreCommerce(
            $client
        );

        return view(
            'commercial.client-validation.show',
            compact(
                'client',
                'registre'
            )
        );
    }

    /**
     * Consultation sécurisée du registre par le commercial.
     */
    public function commercialRegistre(
        Request $request,
        User $client
    ): BinaryFileResponse {
        $this->ensureCommercial(
            $request->user()
        );

        abort_unless(
            $client->role === 'client',
            404
        );

        $registre = $this->registreCommerce(
            $client
        );

        abort_unless($registre, 404);

        return $this->openDocument($registre);
    }

    /**
     * Validation du compte par le commercial.
     */
    public function approve(
        Request $request,
        User $client
    ): RedirectResponse {
        $this->ensureCommercial(
            $request->user()
        );

        abort_unless(
            $client->role === 'client',
            404
        );

        abort_unless(
            $client->hasVerifiedEmail(),
            422
        );

        $registre = $this->registreCommerce(
            $client
        );

        if (! $registre) {
            return back()->with(
                'error',
                'Aucun registre de commerce n’est associé à ce client.'
            );
        }

        $client->forceFill([
            'statut_validation_compte' => 'actif',
            'motif_refus_validation' => null,
            'validation_compte_at' => now(),
            'validation_compte_par' => $request->user()->id_user,
        ])->save();

        $client->notify(
            new ClientValidationNotification(
                $client->id_user,
                'Compte validé',
                'Votre registre de commerce a été validé. Votre espace client est maintenant actif.'
            )
        );

        return redirect()
            ->route(
                'commercial.clients.validation.show',
                $client
            )
            ->with(
                'success',
                'Le compte client a été validé.'
            );
    }

    /**
     * Refus du registre avec motif obligatoire.
     *
     * Décision de conception du prototype :
     * le refus et le motif n'ont pas été explicitement détaillés
     * par l'encadrant mais complètent le workflow de validation.
     */
    public function reject(
        Request $request,
        User $client
    ): RedirectResponse {
        $this->ensureCommercial(
            $request->user()
        );

        abort_unless(
            $client->role === 'client',
            404
        );

        $validated = $request->validate([
            'motif_refus_validation' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $client->forceFill([
            'statut_validation_compte' => 'refuse',
            'motif_refus_validation'
                => $validated['motif_refus_validation'],
            'validation_compte_at' => now(),
            'validation_compte_par' => $request->user()->id_user,
        ])->save();

        $client->notify(
            new ClientValidationNotification(
                $client->id_user,
                'Registre de commerce refusé',
                'Votre registre de commerce n’a pas été validé. Consultez le motif et remplacez le document.'
            )
        );

        return redirect()
            ->route(
                'commercial.clients.validation.show',
                $client
            )
            ->with(
                'success',
                'Le registre de commerce a été refusé et le motif a été enregistré.'
            );
    }

    /**
     * Cherche le document de compte correspondant au registre
     * de commerce. Le fallback permet de rester compatible avec
     * les anciens noms de type_document déjà présents.
     */
    private function registreCommerce(
        User $client
    ): ?Document {
        $base = Document::query()
            ->where(
                'id_user',
                $client->id_user
            )
            ->whereNull('id_boutique');

        $registre = (clone $base)
            ->where(function ($query) {
                $query
                    ->where(
                        'type_document',
                        'like',
                        '%registre%'
                    )
                    ->orWhere(
                        'type_document',
                        'like',
                        '%commerce%'
                    );
            })
            ->latest('id_document')
            ->first();

        if ($registre) {
            return $registre;
        }

        /*
         * Compatibilité avec les premières données du prototype :
         * si un seul document de niveau compte existe mais possède
         * un ancien libellé, on le considère comme le justificatif
         * d'inscription.
         */
        return $base
            ->latest('id_document')
            ->first();
    }

    private function openDocument(
        Document $document
    ): BinaryFileResponse {
        $disk = Storage::disk('local');

        abort_unless(
            $document->chemin_fichier
            && $disk->exists(
                $document->chemin_fichier
            ),
            404
        );

        return response()->file(
            $disk->path(
                $document->chemin_fichier
            )
        );
    }

    private function ensureClient(
        ?User $user
    ): void {
        abort_unless(
            $user && $user->role === 'client',
            403
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

    private function notifyCommercials(
        User $client,
        string $titre,
        string $message
    ): void {
        User::query()
            ->where('role', 'commercial')
            ->get()
            ->each(
                fn (User $commercial) =>
                    $commercial->notify(
                        new ClientValidationNotification(
                            $client->id_user,
                            $titre,
                            $message
                        )
                    )
            );
    }
}
