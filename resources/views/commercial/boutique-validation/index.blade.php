@extends('layouts.app')

@section('title', 'Validation boutiques')
@section('breadcrumb', 'Espace Commercial / Validation boutiques')

@section('content')

<div class="listing-hero">
    <div>
        <div class="page-eyebrow">
            Espace commercial · Justificatifs
        </div>

        <h1 class="page-title">
            Validation des boutiques
        </h1>

        <p class="page-subtitle">
            Contrôlez les justificatifs des boutiques avant leur utilisation
            dans une commande.
        </p>
    </div>

    <div class="listing-count">
        <strong>{{ $compteurs['en_attente_validation'] }}</strong>
        <span>en attente</span>
    </div>
</div>


<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="stat-tile">
            <div>
                <div class="stat-label">En attente</div>
                <div class="stat-number">
                    {{ $compteurs['en_attente_validation'] }}
                </div>
            </div>

            <span class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-tile">
            <div>
                <div class="stat-label">Refusées</div>
                <div class="stat-number">
                    {{ $compteurs['refusee'] }}
                </div>
            </div>

            <span class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-tile">
            <div>
                <div class="stat-label">Validées</div>
                <div class="stat-number">
                    {{ $compteurs['validee'] }}
                </div>
            </div>

            <span class="stat-icon">
                <i class="bi bi-shop-window"></i>
            </span>
        </div>
    </div>

</div>


<div class="app-card overflow-hidden">

    <div class="section-header">
        <div>
            <h2 class="section-title">
                Boutiques clients
            </h2>

            <p class="section-subtitle">
                Suivi des justificatifs déposés.
            </p>
        </div>

        <form
            method="GET"
            action="{{ route('commercial.boutiques.validation.index') }}"
        >
            <select
                name="statut"
                class="form-select"
                onchange="this.form.submit()"
            >
                <option value="">
                    Tous les statuts
                </option>

                <option
                    value="en_attente_validation"
                    {{ $statut === 'en_attente_validation' ? 'selected' : '' }}
                >
                    En attente
                </option>

                <option
                    value="refusee"
                    {{ $statut === 'refusee' ? 'selected' : '' }}
                >
                    Refusées
                </option>

                <option
                    value="validee"
                    {{ $statut === 'validee' ? 'selected' : '' }}
                >
                    Validées
                </option>
            </select>
        </form>
    </div>


    <div class="table-responsive">

        <table class="table app-table responsive-table mb-0">

            <thead>
                <tr>
                    <th>Boutique</th>
                    <th>Client</th>
                    <th>Wilaya</th>
                    <th>Validation</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($boutiques as $boutique)

                    <tr class="data-row">

                        <td
                            data-label="Boutique"
                            class="primary-cell"
                        >
                            <div class="item-identity">
                                <span class="item-icon">
                                    <i class="bi bi-shop"></i>
                                </span>

                                <div>
                                    <div class="item-name">
                                        {{ $boutique->nom_boutique }}
                                    </div>

                                    <div class="item-caption">
                                        Boutique #{{ $boutique->id_boutique }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td data-label="Client">
                            {{ optional($boutique->client)->name ?? '—' }}
                        </td>

                        <td data-label="Wilaya">
                            {{ $boutique->wilaya }}
                        </td>

                        <td data-label="Validation">

                            @if ($boutique->statut_validation_justificatif === 'validee')
                                <span class="status-pill status-done">
                                    Validée
                                </span>
                            @elseif ($boutique->statut_validation_justificatif === 'refusee')
                                <span class="status-pill status-refused">
                                    Refusée
                                </span>
                            @else
                                <span class="status-pill status-wait">
                                    En attente
                                </span>
                            @endif

                        </td>

                        <td
                            data-label="Action"
                            class="actions-cell text-end"
                        >
                            <a
                                href="{{ route('commercial.boutiques.validation.show', $boutique) }}"
                                class="btn btn-soft btn-sm"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Examiner
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr class="empty-row">
                        <td colspan="5" class="text-center">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-shop"></i>
                                </div>

                                <h3>Aucun dossier</h3>

                                <p>
                                    Aucune boutique ne correspond au filtre
                                    sélectionné.
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
