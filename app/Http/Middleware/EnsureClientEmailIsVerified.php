<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientEmailIsVerified
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        /*
         * Si l'utilisateur est un client
         * ET que son email n'est pas vérifié,
         * on l'envoie vers la page de vérification.
         */
        if (
            $user &&
            $user->role === 'client' &&
            ! $user->hasVerifiedEmail()
        ) {
            return redirect()
                ->route('verification.notice');
        }

        /*
         * Commercial ou client vérifié :
         * accès autorisé.
         */
        return $next($request);
    }
}