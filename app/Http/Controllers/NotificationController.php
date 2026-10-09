<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NotificationController extends Controller
{
    public function read(Request $request, string $notification): RedirectResponse
    {
        $user = $request->user();

        $notificationModel = $user
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail();

        $notificationModel->markAsRead();

        $data = $notificationModel->data;

        if (isset($data['id_client_validation'])) {
            if (
                $user->role === 'commercial'
                && Route::has('commercial.clients.validation.show')
            ) {
                return redirect()->route(
                    'commercial.clients.validation.show',
                    $data['id_client_validation']
                );
            }

            if (
                $user->role === 'client'
                && Route::has('client.validation.status')
            ) {
                return redirect()->route('client.validation.status');
            }
        }

        if (isset($data['id_boutique_validation'])) {
            if (
                $user->role === 'commercial'
                && Route::has('commercial.boutiques.validation.show')
            ) {
                return redirect()->route(
                    'commercial.boutiques.validation.show',
                    $data['id_boutique_validation']
                );
            }

            if (
                $user->role === 'client'
                && Route::has('boutiques.show')
            ) {
                return redirect()->route(
                    'boutiques.show',
                    $data['id_boutique_validation']
                );
            }
        }

        if (isset($data['id_commande'])) {
            if (
                $user->role === 'commercial'
                && Route::has('commercial.commandes.show')
            ) {
                return redirect()->route(
                    'commercial.commandes.show',
                    $data['id_commande']
                );
            }

            if (Route::has('commandes.show')) {
                return redirect()->route(
                    'commandes.show',
                    $data['id_commande']
                );
            }
        }

        if (isset($data['id_reclamation'])) {
            if (
                $user->role === 'commercial'
                && Route::has('commercial.reclamations.show')
            ) {
                return redirect()->route(
                    'commercial.reclamations.show',
                    $data['id_reclamation']
                );
            }

            if (Route::has('reclamations.show')) {
                return redirect()->route(
                    'reclamations.show',
                    $data['id_reclamation']
                );
            }
        }

        return redirect()->back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}
