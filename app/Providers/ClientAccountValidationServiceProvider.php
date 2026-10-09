<?php

namespace App\Providers;

use App\Http\Controllers\ClientValidationController;
use App\Models\User;
use App\Notifications\ClientValidationNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ClientAccountValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        

        

        /*
         * RÈGLE CONFIRMÉE PAR L'ENCADRANT :
         * après vérification de l'e-mail, le compte client reste
         * en attente jusqu'à validation du registre de commerce
         * par le commercial.
         */
        Event::listen(
            Verified::class,
            function (Verified $event): void {
                $user = $event->user;

                if ($user->role !== 'client') {
                    return;
                }

                $user->forceFill([
                    'statut_validation_compte'
                        => 'en_attente_validation',
                    'motif_refus_validation' => null,
                    'validation_compte_at' => null,
                    'validation_compte_par' => null,
                ])->save();

                User::query()
                    ->where('role', 'commercial')
                    ->get()
                    ->each(
                        fn (User $commercial) =>
                            $commercial->notify(
                                new ClientValidationNotification(
                                    $user->id_user,
                                    'Nouveau compte à valider',
                                    'Le client '
                                        . $user->name
                                        . ' a vérifié son e-mail. Son registre de commerce doit être contrôlé.'
                                )
                            )
                    );
            }
        );

        /*
         * Routes client : elles restent accessibles même lorsque
         * le compte est en attente/refusé.
         */
        Route::middleware([
            'web',
            'auth',
        ])
            ->prefix('validation-compte')
            ->name('client.validation.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ClientValidationController::class,
                        'status',
                    ]
                )->name('status');

                Route::get(
                    '/registre',
                    [
                        ClientValidationController::class,
                        'clientRegistre',
                    ]
                )->name('registre');

                Route::post(
                    '/remplacer-registre',
                    [
                        ClientValidationController::class,
                        'replaceDocument',
                    ]
                )->name('replace-document');
            });

        /*
         * Routes commerciales.
         * Le contrôleur effectue également un contrôle du rôle.
         */
        Route::middleware([
            'web',
            'auth',
        ])
            ->prefix('commercial/clients-validation')
            ->name('commercial.clients.validation.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ClientValidationController::class,
                        'commercialIndex',
                    ]
                )->name('index');

                Route::get(
                    '/{client}',
                    [
                        ClientValidationController::class,
                        'commercialShow',
                    ]
                )->name('show');

                Route::get(
                    '/{client}/registre',
                    [
                        ClientValidationController::class,
                        'commercialRegistre',
                    ]
                )->name('registre');

                Route::post(
                    '/{client}/valider',
                    [
                        ClientValidationController::class,
                        'approve',
                    ]
                )->name('approve');

                Route::post(
                    '/{client}/refuser',
                    [
                        ClientValidationController::class,
                        'reject',
                    ]
                )->name('reject');
            });
    }
}
