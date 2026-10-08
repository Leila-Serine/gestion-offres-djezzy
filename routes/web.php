<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoutiqueController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\RegisterController;

use App\Jobs\SendVerificationEmail;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\CommercialCommandeController;
use App\Http\Controllers\CommercialReclamationController;
use App\Http\Controllers\NotificationController;



/*
|--------------------------------------------------------------------------
| Page d'accueil
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.attempt');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Inscription publique
|--------------------------------------------------------------------------
*/

Route::get('/register', [RegisterController::class, 'create'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'store'])
    ->name('register.store');


/*
|--------------------------------------------------------------------------
| Routes accessibles uniquement aux utilisateurs connectés
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

/*
     * Notifications.
     */
    Route::middleware('auth')->group(function () {

    Route::post(
        '/notifications/{notification}/read',
        [NotificationController::class, 'read']
    )->name('notifications.read');


    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'readAll']
    )->name('notifications.read-all');

});

    /*
    |--------------------------------------------------------------------------
    | Vérification de l'adresse e-mail
    |--------------------------------------------------------------------------
    |
    | Ces routes doivent être accessibles à un utilisateur connecté
    | même s'il n'a PAS encore vérifié son email.
    |
    */

    /*
     * Page affichée après l'inscription.
     */
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');


    /*
     * Route utilisée lorsque l'utilisateur clique
     * sur le lien reçu par email.
     */
    Route::get(
        '/email/verify/{id}/{hash}',
        function (EmailVerificationRequest $request) {

            /*
             * Marque l'adresse email comme vérifiée.
             *
             * email_verified_at passe de NULL
             * à une date.
             */
            $request->fulfill();

            return redirect()
                ->route('offres.index')
                ->with(
                    'success',
                    'Votre adresse e-mail a été vérifiée avec succès.'
                );
        }
    )
        ->middleware([
            'signed',
            'throttle:6,1',
        ])
        ->name('verification.verify');


    /*
     * Permet de renvoyer un nouveau lien
     * de vérification.
     */
    Route::post(
        '/email/verification-notification',
        function (Request $request) {

            /*
             * On évite d'envoyer un nouvel email
             * si l'utilisateur est déjà vérifié.
             */
            if (! $request->user()->hasVerifiedEmail()) {

                SendVerificationEmail::dispatch(
                    $request->user()
                );
            }

            return back()->with(
                'status',
                'verification-link-sent'
            );
        }
    )
        ->middleware('throttle:6,1')
        ->name('verification.send');


    /*
    |--------------------------------------------------------------------------
    | Consultation des offres
    |--------------------------------------------------------------------------
    |
    | Commercial :
    | → accès normal.
    |
    | Client vérifié :
    | → accès normal.
    |
    | Client non vérifié :
    | → redirection vers /email/verify.
    |
    */

    Route::get('/offres', [OffreController::class, 'index'])
        ->middleware('client.verified')
        ->name('offres.index');


    /*
    |--------------------------------------------------------------------------
    | Gestion des offres réservée au commercial
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:commercial')->group(function () {

        /*
         * Cette route statique doit rester avant
         * les routes /offres/{offre}.
         */
        Route::get('/offres/create', [OffreController::class, 'create'])
            ->name('offres.create');

        Route::post('/offres', [OffreController::class, 'store'])
            ->name('offres.store');

        Route::get('/offres/{offre}/edit', [OffreController::class, 'edit'])
            ->whereNumber('offre')
            ->name('offres.edit');

        Route::put('/offres/{offre}', [OffreController::class, 'update'])
            ->whereNumber('offre')
            ->name('offres.update');

        Route::delete('/offres/{offre}', [OffreController::class, 'destroy'])
            ->whereNumber('offre')
            ->name('offres.destroy');

            Route::get(
    '/commercial/commandes',

    [CommercialCommandeController::class, 'index']


    )->name('commercial.commandes.index');

Route::get(
    '/commercial/commandes/{commande}',
    [CommercialCommandeController::class, 'show']
)
    ->whereNumber('commande')
    ->name('commercial.commandes.show');

Route::post(
    '/commercial/commandes/{commande}/valider',
    [
        CommercialCommandeController::class,
        'validateCommande',
    ]
)
    ->whereNumber('commande')
    ->name('commercial.commandes.validate');

Route::post(
    '/commercial/commandes/{commande}/refuser',
    [
        CommercialCommandeController::class,
        'refuseCommande',
    ]
)
    ->whereNumber('commande')
    ->name('commercial.commandes.refuse');




    Route::get(
    '/commercial/reclamations',
    [CommercialReclamationController::class, 'index']
)->name('commercial.reclamations.index');

Route::get(
    '/commercial/reclamations/{reclamation}',
    [CommercialReclamationController::class, 'show']
)
    ->whereNumber('reclamation')
    ->name('commercial.reclamations.show');

Route::post(
    '/commercial/reclamations/{reclamation}/messages',
    [CommercialReclamationController::class, 'storeMessage']
)
    ->whereNumber('reclamation')
    ->name('commercial.reclamations.messages.store');

Route::post(
    '/commercial/reclamations/{reclamation}/resolve',
    [CommercialReclamationController::class, 'resolve']
)
    ->whereNumber('reclamation')
    ->name('commercial.reclamations.resolve');
    });


    /*
    |--------------------------------------------------------------------------
    | Détail d'une offre
    |--------------------------------------------------------------------------
    |
    | Même logique que la liste :
    | - commercial autorisé ;
    | - client vérifié autorisé ;
    | - client non vérifié bloqué.
    |
    */

    Route::get('/offres/{offre}', [OffreController::class, 'show'])
        ->middleware('client.verified')
        ->whereNumber('offre')
        ->name('offres.show');


    /*
    |--------------------------------------------------------------------------
    | Espace client
    |--------------------------------------------------------------------------
    |
    | Ces fonctionnalités nécessitent :
    |
    | 1. utilisateur connecté        → auth
    | 2. rôle client                 → role:client
    | 3. email vérifié               → verified
    |
    */

    Route::middleware([
        'role:client',
        'verified',
    ])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Boutiques
        |--------------------------------------------------------------------------
        */

        Route::get('/boutiques', [BoutiqueController::class, 'index'])
            ->name('boutiques.index');

        Route::get('/boutiques/create', [BoutiqueController::class, 'create'])
            ->name('boutiques.create');

        Route::post('/boutiques', [BoutiqueController::class, 'store'])
            ->name('boutiques.store');

        Route::get('/boutiques/{boutique}', [BoutiqueController::class, 'show'])
            ->whereNumber('boutique')
            ->name('boutiques.show');

        Route::get('/boutiques/{boutique}/edit', [BoutiqueController::class, 'edit'])
            ->whereNumber('boutique')
            ->name('boutiques.edit');

        Route::put('/boutiques/{boutique}', [BoutiqueController::class, 'update'])
            ->whereNumber('boutique')
            ->name('boutiques.update');

        Route::delete('/boutiques/{boutique}', [BoutiqueController::class, 'destroy'])
            ->whereNumber('boutique')
            ->name('boutiques.destroy');
            Route::get(
    '/boutiques/{boutique}/document/{document}',
    [BoutiqueController::class, 'document']
)->name('boutiques.document');


        /*
        |--------------------------------------------------------------------------
        | Commandes
        |--------------------------------------------------------------------------
        */

        Route::get('/commandes', [CommandeController::class, 'index'])
            ->name('commandes.index');

        Route::get('/commandes/create', [CommandeController::class, 'create'])
            ->name('commandes.create');

        Route::post('/commandes', [CommandeController::class, 'store'])
            ->name('commandes.store');

        Route::get('/commandes/{commande}', [CommandeController::class, 'show'])
            ->whereNumber('commande')
            ->name('commandes.show');


/*
        |--------------------------------------------------------------------------
        | Réclamations
        |--------------------------------------------------------------------------     
    */
            Route::get(
    '/reclamations',
    [ReclamationController::class, 'index']
)->name('reclamations.index');

Route::get(
    '/reclamations/create',
    [ReclamationController::class, 'create']
)->name('reclamations.create');

Route::post(
    '/reclamations',
    [ReclamationController::class, 'store']
)->name('reclamations.store');

Route::get(
    '/reclamations/{reclamation}',
    [ReclamationController::class, 'show']
)
    ->whereNumber('reclamation')
    ->name('reclamations.show');
    }
    );

Route::post(
    '/reclamations/{reclamation}/messages',
    [ReclamationController::class, 'storeMessage']
)
    ->whereNumber('reclamation')
    ->name('reclamations.messages.store');

}


);

