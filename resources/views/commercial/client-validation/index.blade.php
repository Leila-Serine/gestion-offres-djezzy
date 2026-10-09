@extends('layouts.app')

@section('title', 'Validation clients')
@section('breadcrumb', 'Espace Commercial / Validation clients')

@section('content')

<div class="listing-hero">

    <div>
        <div class="page-eyebrow">
            Espace commercial · Comptes clients
        </div>

        <h1 class="page-title">
            Validation des clients
        </h1>

        <p class="page-subtitle">
            Contrôlez les registres de commerce avant
            d'activer les comptes clients.
        </p>
    </div>

    <div class="listing-count">
        <strong>
            {{ $compteurs['en_attente_validation'] }}
        </strong>

        <span>
            en attente
        </span>
    </div>

</div>


<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="stat-tile">
            <div>
                <div class="stat-label">
                    En attente
                </div>

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
                <div class="stat-label">
                    Refusés
                </div>

                <div class="stat-number">
                    {{ $compteurs['refuse'] }}
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
                <div class="stat-label">
                    Actifs
                </div>

                <div class="stat-number">
                    {{ $compteurs['actif'] }}
                </div>
            </div>

            <span class="stat-icon">
                <i class="bi bi-person-check"></i>
            </span>
        </div>
    </div>

</div>


<div class="app-card overflow-hidden">

    <div class="section-header">

        <div>
            <h2 class="section-title">
                Comptes clients
            </h2>

            <p class="section-subtitle">
                Clients ayant déjà vérifié leur adresse e-mail.
            </p>
        </div>

        <form
            method="GET"
            action="{{ route(
                'commercial.clients.validation.index'
            ) }}"
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
                    {{ $statut === 'en_attente_validation'
                        ? 'selected'
                        : '' }}
                >
                    En attente
                </option>

                <option
                    value="refuse"
                    {{ $statut === 'refuse'
                        ? 'selected'
                        : '' }}
                >
                    Refusés
                </option>

                <option
                    value="actif"
                    {{ $statut === 'actif'
                        ? 'selected'
                        : '' }}
                >
                    Actifs
                </option>
            </select>
        </form>

    </div>


    <div class="table-responsive">

        <table class="table app-table responsive-table mb-0">

            <thead>
                <tr>
                    <th>Client</th>
                    <th>E-mail</th>
                    <th>Statut</th>
                    <th>Validation</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>


            <tbody>

                @forelse ($clients as $client)

                    <tr class="data-row">

                        <td
                            data-label="Client"
                            class="primary-cell"
                        >
                            <div class="item-identity">

                                <span class="item-icon">
                                    <i class="bi bi-person"></i>
                                </span>

                                <div>
                                    <div class="item-name">
                                        {{ $client->name }}
                                    </div>

                                    <div class="item-caption">
                                        Client #{{ $client->id_user }}
                                    </div>
                                </div>

                            </div>
                        </td>


                        <td data-label="E-mail">
                            {{ $client->email }}
                        </td>


                        <td data-label="Statut">

                            @if (
                                $client->statut_validation_compte
                                === 'actif'
                            )
                                <span class="status-pill status-done">
                                    Actif
                                </span>

                            @elseif (
                                $client->statut_validation_compte
                                === 'refuse'
                            )
                                <span class="status-pill status-refused">
                                    Refusé
                                </span>

                            @else
                                <span class="status-pill status-wait">
                                    En attente
                                </span>
                            @endif

                        </td>


                        <td data-label="Validation">

                            @if ($client->validation_compte_at)
                                <span class="muted-value">
                                    {{ \Illuminate\Support\Carbon::parse(
                                        $client->validation_compte_at
                                    )->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span class="muted-value">
                                    —
                                </span>
                            @endif

                        </td>


                        <td
                            data-label="Action"
                            class="actions-cell text-end"
                        >
                            <a
                                href="{{ route(
                                    'commercial.clients.validation.show',
                                    $client
                                ) }}"
                                class="btn btn-soft btn-sm"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Examiner
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr class="empty-row">
                        <td
                            colspan="5"
                            class="text-center"
                        >
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-person-check"></i>
                                </div>

                                <h3>
                                    Aucun client
                                </h3>

                                <p>
                                    Aucun dossier ne correspond
                                    au filtre sélectionné.
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
