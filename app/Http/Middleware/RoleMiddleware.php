<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Vérifie que l'utilisateur possède le rôle demandé.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {
        // Aucun utilisateur connecté.
        if (! $request->user()) {
            abort(401);
        }

        // Utilisateur connecté, mais mauvais rôle.
        if ($request->user()->role !== $role) {
            abort(403);
        }

        // Le rôle est correct : la requête continue.
        return $next($request);
    }
}