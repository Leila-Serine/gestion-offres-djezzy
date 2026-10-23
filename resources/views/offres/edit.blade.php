@extends('layouts.app')

@section('title', 'Modifier une offre')
@section('breadcrumb', 'Offres / Modification')

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Modifier l'offre</h1>
            <p class="page-subtitle mb-0">
                Modifiez les informations, les caractéristiques et le stock régional.
            </p>
        </div>

        <a href="{{ route('offres.index') }}" class="btn btn-outline-secondary">
            Retour
        </a>
    </div>

    <form method="POST" action="{{ route('offres.update', $offre) }}">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nom_offre" class="form-label fw-semibold">
                            Nom de l'offre
                        </label>

                        <input
                            id="nom_offre"
                            type="text"
                            name="nom_offre"
                            class="form-control @error('nom_offre') is-invalid @enderror"
                            value="{{ old('nom_offre', $offre->nom_offre) }}"
                            required
                        >

                        @error('nom_offre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="categorie" class="form-label fw-semibold">
                            Catégorie
                        </label>

                        <select
                            id="categorie"
                            name="categorie"
                            class="form-select @error('categorie') is-invalid @enderror"
                            required
                        >
                            <option value="">Choisir...</option>
                            <option
                                value="Prépayée"
                                @selected(old('categorie', $offre->categorie) === 'Prépayée')
                            >
                                Prépayée
                            </option>
                            <option
                                value="Postpayée"
                                @selected(old('categorie', $offre->categorie) === 'Postpayée')
                            >
                                Postpayée
                            </option>

                            @if (!in_array(old('categorie', $offre->categorie), ['Prépayée', 'Postpayée'], true))
                                <option
                                    value="{{ old('categorie', $offre->categorie) }}"
                                    selected
                                >
                                    {{ old('categorie', $offre->categorie) }}
                                </option>
                            @endif
                        </select>

                        @error('categorie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="prix" class="form-label fw-semibold">
                            Prix
                        </label>

                        <div class="input-group">
                            <input
                                id="prix"
                                type="number"
                                name="prix"
                                min="0"
                                step="0.01"
                                class="form-control @error('prix') is-invalid @enderror"
                                value="{{ old('prix', $offre->prix) }}"
                            >
                            <span class="input-group-text">DA</span>
                        </div>

                        @error('prix')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="statut" class="form-label fw-semibold">
                            Statut
                        </label>

                        <select
                            id="statut"
                            name="statut"
                            class="form-select @error('statut') is-invalid @enderror"
                        >
                            <option
                                value="active"
                                @selected(old('statut', $offre->statut) === 'active')
                            >
                                Active
                            </option>
                            <option
                                value="suspendue"
                                @selected(old('statut', $offre->statut) === 'suspendue')
                            >
                                Suspendue
                            </option>
                        </select>

                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="mode_stock_zero" class="form-label fw-semibold">
                            Comportement quand le stock est à zéro
                        </label>

                        <select
                            id="mode_stock_zero"
                            name="mode_stock_zero"
                            class="form-select @error('mode_stock_zero') is-invalid @enderror"
                        >
                            <option
                                value="bientot_disponible"
                                @selected(old('mode_stock_zero', $offre->mode_stock_zero) === 'bientot_disponible')
                            >
                                Afficher « Bientôt disponible »
                            </option>
                            <option
                                value="masquee"
                                @selected(old('mode_stock_zero', $offre->mode_stock_zero) === 'masquee')
                            >
                                Masquer l'offre
                            </option>
                        </select>

                        @error('mode_stock_zero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @include('offres._caracteristiques')
        @include('offres._disponibilites')

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('offres.index') }}" class="btn btn-outline-secondary">
                Annuler
            </a>

            <button type="submit" class="btn btn-danger">
                Enregistrer
            </button>
        </div>
    </form>
@endsection
