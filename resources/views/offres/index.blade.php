@extends('layouts.app')

@section('title', 'Offres')
@section('breadcrumb', 'Gestion des offres')

@section('content')

@php
    $estCommercial = auth()->user()->role === 'commercial';
@endphp

<div class="listing-hero">
    <div>
        <div class="page-eyebrow">
            Catalogue des offres
        </div>

        <h1 class="page-title">
            Offres
        </h1>

        <p class="page-subtitle">
            @if ($estCommercial)
                Gérez le catalogue, les tarifs et le cycle de vie des offres.
            @else
                Consultez les offres actuellement disponibles depuis votre espace client.
            @endif
        </p>
    </div>

    <div class="listing-actions">
        <div class="listing-count">
            <strong>{{ $offres->count() }}</strong>
            <span>
                {{ $estCommercial ? 'offres au total' : 'offres disponibles' }}
            </span>
        </div>

        @if ($estCommercial && Route::has('offres.create'))
            <a
                href="{{ route('offres.create') }}"
                class="btn btn-djezzy px-4"
            >
                <i class="bi bi-plus-lg me-2"></i>
                Ajouter une offre
            </a>
        @endif
    </div>
</div>


<div class="app-card overflow-hidden">

    <div class="section-header">
        <div>
            <h2 class="section-title">
                Liste des offres
            </h2>

            <p class="section-subtitle mb-0">
                {{ $offres->count() }}
                {{ $offres->count() > 1 ? 'offres enregistrées' : 'offre enregistrée' }}
            </p>
        </div>

        <div style="min-width: 240px;">
            <label
                for="rechercheOffre"
                class="visually-hidden"
            >
                Rechercher une offre
            </label>

            <div class="position-relative">
                <i
                    class="bi bi-search position-absolute"
                    style="left: 14px; top: 50%; transform: translateY(-50%);"
                ></i>

                <input
                    id="rechercheOffre"
                    type="search"
                    class="form-control ps-5"
                    placeholder="Rechercher une offre..."
                >
            </div>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table app-table responsive-table mb-0">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Offre</th>
                    <th>Catégorie</th>
                    <th>Tarif</th>

                    @if ($estCommercial)
                        <th>Statut</th>
                    @endif

                    <th>Créée le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody id="offresTableBody">
                @forelse ($offres as $offre)
                    <tr
                        class="data-row offre-row"
                        data-search="{{ strtolower($offre->nom_offre . ' ' . $offre->categorie . ' ' . $offre->statut) }}"
                    >
                        <td data-label="Référence">
                            <span class="text-value">
                                #{{ $offre->id_offre }}
                            </span>
                        </td>

                        <td
                            data-label="Offre"
                            class="primary-cell"
                        >
                            <div class="item-identity">
                                <span class="item-icon">
                                    <i class="bi bi-sim"></i>
                                </span>

                                <div>
                                    <div class="item-name">
                                        {{ $offre->nom_offre }}
                                    </div>

                                    <div class="item-caption">
                                        Offre Djezzy
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td data-label="Catégorie">
                            <span class="category-badge">
                                {{ $offre->categorie }}
                            </span>
                        </td>

                        <td data-label="Tarif">
                            <span class="text-value">
                                {{ number_format((float) $offre->prix, 2, ',', ' ') }} DA
                            </span>
                        </td>

                        @if ($estCommercial)
                            <td data-label="Statut">
                                @if ($offre->statut === 'active')
                                    <span class="status-pill status-done">
                                        Active
                                    </span>
                                @else
                                    <span class="status-pill status-wait">
                                        Suspendue
                                    </span>
                                @endif
                            </td>
                        @endif

                        <td data-label="Créée le">
                            <div class="muted-value">
                                <div>
                                    {{ $offre->created_at?->format('d/m/Y') }}
                                </div>

                                <div>
                                    {{ $offre->created_at?->format('H:i') }}
                                </div>
                            </div>
                        </td>

                        <td
                            data-label="Actions"
                            class="actions-cell text-end"
                        >
                            <div class="d-flex justify-content-end gap-2">
                                <a
                                    href="{{ route('offres.show', $offre) }}"
                                    class="btn btn-soft btn-sm"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Voir
                                </a>

                                @if ($estCommercial)
                                    <a
                                        href="{{ route('offres.edit', $offre) }}"
                                        class="btn btn-outline-djezzy btn-sm"
                                    >
                                        <i class="bi bi-pencil me-1"></i>
                                        Modifier
                                    </a>

                                    @if ($offre->statut === 'active')
                                        <form
                                            method="POST"
                                            action="{{ route('offres.update', $offre) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Suspendre cette offre ? Elle ne sera plus visible par les clients.');"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="action_statut"
                                                value="suspendre"
                                            >

                                            <button
    type="submit"
    class="btn btn-sm"
    style="
        border: 1px solid #d99a00;
        color: #8a5a00;
        background: #fff8e6;
        font-weight: 600;
    "
>
    <i class="bi bi-pause-circle me-1"></i>
    Suspendre
</button>
                                        </form>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('offres.update', $offre) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <input
                                                type="hidden"
                                                name="action_statut"
                                                value="reactiver"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-success btn-sm"
                                            >
                                                <i class="bi bi-play-circle me-1"></i>
                                                Réactiver
                                            </button>
                                        </form>
                                    @endif

                                    @if ((int) $offre->lignes_commande_count === 0)
                                        <form
                                            method="POST"
                                            action="{{ route('offres.destroy', $offre) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Supprimer"
                                                aria-label="Supprimer {{ $offre->nom_offre }}"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td
                            colspan="{{ $estCommercial ? 7 : 6 }}"
                            class="text-center py-5"
                        >
                            <div class="empty-state py-3">
                                <div class="empty-icon">
                                    <i class="bi bi-grid"></i>
                                </div>

                                <h3>Aucune offre</h3>

                                <p>
                                    Aucune offre n'est disponible pour le moment.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('rechercheOffre');
    const rows = document.querySelectorAll('.offre-row');

    if (!input) {
        return;
    }

    input.addEventListener('input', function () {
        const recherche = input.value
            .toLowerCase()
            .trim();

        rows.forEach(function (row) {
            const texte = row.dataset.search || '';

            row.style.display =
                texte.includes(recherche)
                    ? ''
                    : 'none';
        });
    });
});
</script>
@endpush
