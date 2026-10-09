<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(
        Request $request,
        string $notification
    ): RedirectResponse {
        $user = $request->user();

        $notificationModel = $user
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail();

        $notificationModel->markAsRead();

        $data = $notificationModel->data;

        /*
         * Validation des comptes clients.
         */
        if (isset($data['id_client_validation'])) {
            if (
                $user->role === 'commercial'
                && route_has(
                    'commercial.clients.validation.show'
                )
            ) {
                return redirect()->route(
                    'commercial.clients.validation.show',
                    $data['id_client_validation']
                );
            }

            if (
                $user->role === 'client'
                && route_has(
                    'client.validation.status'
                )
            ) {
                return redirect()->route(
                    'client.validation.status'
                );
            }
        }

        /*
         * Commandes.
         */
        if (isset($data['id_commande'])) {
            if (
                $user->role === 'commercial'
                && route_has(
                    'commercial.commandes.show'
                )
            ) {
                return redirect()->route(
                    'commercial.commandes.show',
                    $data['id_commande']
                );
            }

            if (
                route_has('commandes.show')
            ) {
                return redirect()->route(
                    'commandes.show',
                    $data['id_commande']
                );
            }
        }

        /*
         * Réclamations.
         */
        if (isset($data['id_reclamation'])) {
            if (
                $user->role === 'commercial'
                && route_has(
                    'commercial.reclamations.show'
                )
            ) {
                return redirect()->route(
                    'commercial.reclamations.show',
                    $data['id_reclamation']
                );
            }

            if (
                route_has('reclamations.show')
            ) {
                return redirect()->route(
                    'reclamations.show',
                    $data['id_reclamation']
                );
            }
        }

        return redirect()->back();
    }

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

/*
 * Petit helper local au fichier pour éviter de provoquer
 * une exception si une route optionnelle n'est pas encore chargée.
 */
if (! function_exists('route_has')) {
    function route_has(string $name): bool
    {
        return app('router')
            ->getRoutes()
            ->getByName($name) !== null;
    }
}
