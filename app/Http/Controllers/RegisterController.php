<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\RegistrationService;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {
    }


    
    public function create()
    {
        return view('auth.register');
    }



    public function store(RegisterRequest $request)
{
    $data = $request->validated();

    /*
     * Le service crée :
     * - le client
     * - son registre de commerce
     */
    $user = $this->registrationService->register(
        $data,
        $request->file('registre_commerce')
    );

    /*
     * Le nouvel utilisateur devient connecté.
     */
    Auth::login($user);

    /*
     * On régénère la session après connexion.
     */
    $request->session()->regenerate();

    /*
     * On ne contacte pas Gmail ici.
     * On place simplement une tâche dans la Queue.
     */
    SendVerificationEmail::dispatch($user);

    /*
     * On affiche la page :
     * "Vérifiez votre adresse e-mail".
     */
    return redirect()
        ->route('verification.notice');
}
}