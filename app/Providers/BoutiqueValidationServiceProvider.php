<?php

namespace App\Providers;

use App\Http\Controllers\BoutiqueValidationController;
use App\Models\Boutique;
use App\Models\Commande;
use App\Models\Document;
use App\Models\User;
use App\Notifications\BoutiqueValidationNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

class BoutiqueValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Boutique::created(function (Boutique $boutique): void {
            if (request()->routeIs('boutiques.store')) {
                $boutique->forceFill([
                    'statut_validation_justificatif' => 'en_attente_validation',
                    'motif_refus_justificatif' => null,
                    'validation_justificatif_at' => null,
                    'validation_justificatif_par' => null,
                ])->saveQuietly();
            }
        });

        Document::created(function (Document $document): void {
            if (! $document->id_boutique) {
                return;
            }

            $boutique = Boutique::query()
                ->with('client')
                ->find($document->id_boutique);

            if (! $boutique) {
                return;
            }

            if ($boutique->statut_validation_justificatif !== 'en_attente_validation') {
                return;
            }

            User::query()
                ->where('role', 'commercial')
                ->get()
                ->each(
                    fn (User $commercial) =>
                        $commercial->notify(
                            new BoutiqueValidationNotification(
                                $boutique->id_boutique,
                                'Nouvelle boutique à valider',
                                'Le client '
                                    . optional($boutique->client)->name
                                    . ' a déposé le justificatif de la boutique « '
                                    . $boutique->nom_boutique
                                    . ' ».'
                            )
                        )
                );
        });

        Commande::creating(function (Commande $commande): void {
            if (! $commande->id_boutique) {
                return;
            }

            $boutique = Boutique::find($commande->id_boutique);

            if (! $boutique) {
                return;
            }

            $statut = $boutique->statut_validation_justificatif;

            if ($statut !== null && $statut !== 'validee') {
                throw ValidationException::withMessages([
                    'id_boutique' =>
                        'Cette boutique ne peut pas être utilisée pour une commande tant que son justificatif n’est pas validé.',
                ]);
            }
        });

        Route::middleware(['web', 'auth'])
            ->group(function (): void {
                Route::get(
                    '/boutique-validation/statuses',
                    [BoutiqueValidationController::class, 'clientStatuses']
                )->name('boutique-validation.statuses');

                Route::get(
                    '/boutiques/{boutique}/justificatif-validation',
                    [BoutiqueValidationController::class, 'clientDocument']
                )->name('boutiques.validation.document');

                Route::post(
                    '/boutiques/{boutique}/remplacer-justificatif',
                    [BoutiqueValidationController::class, 'replaceDocument']
                )->name('boutiques.validation.replace');
            });

        Route::middleware(['web', 'auth'])
            ->prefix('commercial/boutiques-validation')
            ->name('commercial.boutiques.validation.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [BoutiqueValidationController::class, 'index']
                )->name('index');

                Route::get(
                    '/{boutique}',
                    [BoutiqueValidationController::class, 'show']
                )->name('show');

                Route::get(
                    '/{boutique}/justificatif',
                    [BoutiqueValidationController::class, 'commercialDocument']
                )->name('document');

                Route::post(
                    '/{boutique}/valider',
                    [BoutiqueValidationController::class, 'approve']
                )->name('approve');

                Route::post(
                    '/{boutique}/refuser',
                    [BoutiqueValidationController::class, 'reject']
                )->name('reject');
            });
    }
}
