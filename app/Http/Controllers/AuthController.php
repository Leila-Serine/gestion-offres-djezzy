<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(Request $request)
    {
        /**
         * 1. Validation des champs reçus depuis le formulaire.
         */
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        /**
         * 2. Récupération de la valeur de la case
         *    "Se souvenir de moi".
         *
         * true si cochée, false sinon.
         */
        $remember = $request->boolean('remember');

        /**
         * 3. Tentative d'authentification.
         *
         * Laravel cherche un utilisateur ayant cet email
         * puis vérifie si le mot de passe correspond.
         */
        if (Auth::attempt($credentials, $remember)) {

            /**
             * 4. Régénère l'identifiant de session
             *    après une connexion réussie.
             */
            $request->session()->regenerate();

            /**
             * 5. Redirige l'utilisateur vers la page
             *    qu'il essayait de consulter auparavant.
             *
             * Si aucune page n'était prévue,
             * on l'envoie vers /offres pour l'instant.
             */
            return redirect()->intended('/offres');
        }

        /**
         * Si les identifiants sont incorrects,
         * on retourne au formulaire avec une erreur.
         */
        return back()
            ->withErrors([
                'email' => 'Adresse e-mail ou mot de passe incorrect.',
            ])
            ->onlyInput('email');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        /**
         * Déconnexion de l'utilisateur courant.
         */
        Auth::logout();

        /**
         * Invalide l'ancienne session.
         */
        $request->session()->invalidate();

        /**
         * Génère un nouveau token CSRF.
         */
        $request->session()->regenerateToken();

        /**
         * Retour à la page de connexion.
         */
        return redirect('/login');
    }
}