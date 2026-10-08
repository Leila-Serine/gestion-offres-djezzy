@extends('layouts.guest')
@section('title', 'Créer un compte')

@section('content')
    <div class="page-eyebrow">Espace client</div>
    <h1 class="mb-2">Créer un compte</h1>
    <p class="lead-copy mb-4">Inscrivez-vous pour gérer vos boutiques.</p>

    {{-- Inscription publique : mêmes champs, mêmes erreurs et même envoi multipart. --}}
    <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror" autocomplete="name" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" autocomplete="email" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Chaque œil agit uniquement sur le champ identifié par data-password-toggle. --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="password-field">
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           autocomplete="new-password" required>
                    <button type="button" class="password-toggle" data-password-toggle="password"
                            aria-label="Afficher le mot de passe" aria-controls="password" aria-pressed="false">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                <div class="password-field">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           autocomplete="new-password" required>
                    <button type="button" class="password-toggle" data-password-toggle="password_confirmation"
                            aria-label="Afficher le mot de passe" aria-controls="password_confirmation" aria-pressed="false">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="registre_commerce" class="form-label">Registre de commerce</label>
            <input type="file" id="registre_commerce" name="registre_commerce"
                   accept=".pdf,.jpg,.jpeg,.png"
                   class="form-control @error('registre_commerce') is-invalid @enderror" required>
            <div class="form-text">PDF, JPG ou PNG — maximum 5 Mo.</div>
            @error('registre_commerce')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-djezzy w-100">Créer mon compte</button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">
        <a href="{{ route('login') }}" class="fw-semibold">J'ai déjà un compte</a>
    </p>
@endsection
