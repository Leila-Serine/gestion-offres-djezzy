<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Marque une notification comme lue
     * puis ouvre la ressource concernée.
     */
    public function read(
        Request $request,
        string $notification
    ): RedirectResponse {
        $user = $request->user();

        /*
         * Sécurité :
         * impossible de lire la notification
         * d'un autre utilisateur.
         */
        $notificationModel =
            DatabaseNotification::query()
                ->where(
                    'id',
                    $notification
                )
                ->where(
                    'notifiable_id',
                    $user->id_user
                )
                ->where(
                    'notifiable_type',
                    $user::class
                )
                ->firstOrFail();

        $notificationModel
            ->markAsRead();

        /*
         * =============================
         * NOTIFICATION DE COMMANDE
         * =============================
         */
        $idCommande =
            $notificationModel
                ->data['id_commande']
            ?? null;

        if ($idCommande) {

            if (
                $user->role
                === 'commercial'
            ) {
                return redirect()->route(
                    'commercial.commandes.show',
                    $idCommande
                );
            }

            return redirect()->route(
                'commandes.show',
                $idCommande
            );
        }

        /*
         * =============================
         * NOTIFICATION DE RÉCLAMATION
         * =============================
         */
        $idReclamation =
            $notificationModel
                ->data['id_reclamation']
            ?? null;

        if ($idReclamation) {

            if (
                $user->role
                === 'commercial'
            ) {
                return redirect()->route(
                    'commercial.reclamations.show',
                    $idReclamation
                );
            }

            return redirect()->route(
                'reclamations.show',
                $idReclamation
            );
        }

        return redirect()->back();
    }


    /**
     * Marque toutes les notifications
     * comme lues.
     */
    public function readAll(
        Request $request
    ): RedirectResponse {
        $request
            ->user()
            ->unreadNotifications
            ->markAsRead();

        return redirect()->back();
    }
}