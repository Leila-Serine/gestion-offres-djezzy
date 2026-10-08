@extends('layouts.guest')
@section('title', 'Connexion')

@section('content')
    <div class="page-eyebrow">Bienvenue</div>
    <h1 class="mb-2">Connexion</h1>
    <p class="lead-copy mb-4">Accédez à votre espace professionnel.</p>

    {{-- Les erreurs d'authentification restent visibles avant le formulaire. --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="{{ old('email') }}" autocomplete="email" required autofocus>
        </div>

        {{-- Le bouton garde le mot de passe masqué au chargement et ne soumet jamais le formulaire. --}}
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="password-field">
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       autocomplete="current-password" required>
                <button type="button" class="password-toggle" data-password-toggle="password"
                        aria-label="Afficher le mot de passe" aria-controls="password" aria-pressed="false">
                    <i class="bi bi-eye" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label small" for="remember">Se souvenir de moi</label>
        </div>

        <button type="submit" class="btn btn-djezzy w-100">
            <i class="bi bi-arrow-right me-2" aria-hidden="true"></i>Se connecter
        </button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">
        Vous n'avez pas de compte ? <a href="{{ route('register') }}" class="fw-semibold">Créer un compte client</a>
    </p>
@endsection
