@extends('layouts.app')

@section('title', 'Nouvelle réclamation')
@section('breadcrumb', 'Espace Client / Nouvelle réclamation')

@section('content')
    <div class="content-narrow mx-auto">
        <a class="back-link" href="{{ route('reclamations.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i>Retour aux réclamations</a>
        <div class="page-heading">
            <div><div class="page-eyebrow">Espace Client · Assistance</div><h1 class="page-title">Nouvelle réclamation</h1><p class="page-subtitle">Décrivez clairement votre demande afin de faciliter son traitement.</p></div>
        </div>

        <section class="app-card" aria-labelledby="reclamation-form-title">
            <div class="section-header"><div><h2 id="reclamation-form-title" class="section-title">Votre demande</h2><p class="section-subtitle">Renseignez l'objet et votre message.</p></div><span class="item-icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span></div>
            <div class="section-body">
                {{-- La route, le jeton CSRF et les noms des champs restent ceux du formulaire d'origine. --}}
                <form method="POST" action="{{ route('reclamations.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="objet" class="form-label">Objet</label>
                        <input type="text" id="objet" name="objet" value="{{ old('objet') }}" class="form-control @error('objet') is-invalid @enderror" placeholder="Ex. Problème concernant ma commande" required>
                        @error('objet') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label">Description</label>
                        <textarea id="message" name="message" rows="7" class="form-control @error('message') is-invalid @enderror" placeholder="Décrivez votre problème..." required>{{ old('message') }}</textarea>
                        @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-actions justify-content-end">
                        <a href="{{ route('reclamations.index') }}" class="btn btn-soft px-4">Annuler</a>
                        <button type="submit" class="btn btn-djezzy px-4"><i class="bi bi-send me-2" aria-hidden="true"></i>Envoyer la réclamation</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
