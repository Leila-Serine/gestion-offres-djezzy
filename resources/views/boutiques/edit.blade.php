@extends('layouts.app')

@section('title', 'Modifier une boutique')
@section('breadcrumb', 'Mes boutiques / Modifier')

@section('content')

<div class="page-heading">
    <div>
        <div class="page-eyebrow">
            Mes boutiques
        </div>

        <h1 class="page-title">
            Modifier {{ $boutique->nom_boutique }}
        </h1>

        <p class="page-subtitle">
            Mettez à jour les informations de votre boutique.
        </p>
    </div>

    <div class="page-heading-actions">
        <a
            href="{{ route('boutiques.show', $boutique) }}"
            class="btn btn-soft px-4"
        >
            <i class="bi bi-arrow-left me-2"></i>
            Retour
        </a>
    </div>
</div>


<div class="app-card content-medium">

    <div class="section-header">
        <div>
            <h2 class="section-title">
                Informations de la boutique
            </h2>

            <p class="section-subtitle">
                Coordonnées et justificatif du local
            </p>
        </div>

        <span class="item-icon">
            <i class="bi bi-shop"></i>
        </span>
    </div>


    <form
        method="POST"
        action="{{ route('boutiques.update', $boutique) }}"
        enctype="multipart/form-data"
        class="section-body"
    >
        @csrf
        @method('PUT')

        @include('boutiques._form')

        <div class="form-actions mt-4">
            <button
                type="submit"
                class="btn btn-djezzy px-4"
            >
                <i class="bi bi-check-lg me-2"></i>
                Enregistrer les modifications
            </button>

            <a
                href="{{ route('boutiques.show', $boutique) }}"
                class="btn btn-soft px-4"
            >
                Annuler
            </a>
        </div>
    </form>

</div>

@endsection
