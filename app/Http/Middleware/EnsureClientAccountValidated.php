<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientAccountValidated
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        /*
         * Visiteur non connecté :
         * ce middleware ne gère pas l'authentification.
         */
        if (! $user) {
            return $next($request);
        }

        /*
         * Le commercial n'est jamais concerné
         * par la validation du registre de commerce.
         */
        if ($user->role !== 'client') {
            return $next($request);
        }

        /*
         * Tant que l'e-mail n'est pas vérifié,
         * le middleware client.verified existant
         * garde la responsabilité du contrôle.
         */
        if (! $user->hasVerifiedEmail()) {
            return $next($request);
        }

        /*
         * Routes qui doivent rester accessibles
         * aux clients en attente ou refusés.
         */
        if (
            $request->routeIs('client.validation.*')
            || $request->routeIs('verification.*')
            || $request->routeIs('notifications.*')
            || $request->routeIs('logout')
        ) {
            return $next($request);
        }

        /*
         * COMPATIBILITÉ AVEC L'EXISTANT
         *
         * Les anciens comptes et les anciens tests
         * ont été créés avant l'ajout du champ
         * statut_validation_compte.
         *
         * null signifie donc ici :
         * ancien compte / donnée historique.
         *
         * On les laisse fonctionner normalement.
         */
        if ($user->statut_validation_compte === null) {
            return $next($request);
        }

        /*
         * Seuls les comptes explicitement marqués
         * "actif" peuvent accéder à l'espace client.
         */
        if ($user->statut_validation_compte !== 'actif') {
            return redirect()->route(
                'client.validation.status'
            );
        }

        return $next($request);
    }
}