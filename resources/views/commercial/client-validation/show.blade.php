@extends('layouts.app')

@section('title', 'Validation client')
@section(
    'breadcrumb',
    'Espace Commercial / Validation clients / Détail'
)

@section('content')

<a
    href="{{ route(
        'commercial.clients.validation.index'
    ) }}"
    class="back-link"
>
    <i class="bi bi-arrow-left"></i>
    Validation des clients
</a>


<div class="page-heading">

    <div>
        <div class="page-eyebrow">
            Dossier client
        </div>

        <h1 class="page-title">
            {{ $client->name }}
        </h1>

        <p class="page-subtitle">
            Vérification du registre de commerce
            avant activation du compte.
        </p>
    </div>

    <div>

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
                En attente de validation
            </span>
        @endif

    </div>

</div>


<div class="row g-4">

    <div class="col-lg-7">

        <div class="app-card">

            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        Informations du client
                    </h2>

                    <p class="section-subtitle">
                        Compte et justificatif d'inscription
                    </p>
                </div>

                <span class="item-icon">
                    <i class="bi bi-person"></i>
                </span>
            </div>


            <div class="section-body">

                <dl class="detail-grid mb-4">

                    <div class="detail-field">
                        <dt>Identifiant</dt>
                        <dd>#{{ $client->id_user }}</dd>
                    </div>

                    <div class="detail-field">
                        <dt>Nom</dt>
                        <dd>{{ $client->name }}</dd>
                    </div>

                    <div class="detail-field">
                        <dt>Adresse e-mail</dt>
                        <dd>{{ $client->email }}</dd>
                    </div>

                    <div class="detail-field">
                        <dt>E-mail vérifié</dt>
                        <dd>
                            @if ($client->email_verified_at)
                                <span class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Oui
                                </span>
                            @else
                                <span class="text-danger">
                                    Non
                                </span>
                            @endif
                        </dd>
                    </div>

                </dl>


                <h3 class="h6 fw-bold mb-3">
                    Registre de commerce
                </h3>


                @if ($registre)

                    <div
                        class="d-flex align-items-center
                               justify-content-between
                               gap-3 border rounded-3 p-3"
                    >
                        <div class="d-flex align-items-center gap-3">

                            <span class="item-icon">
                                <i class="bi bi-file-earmark-check"></i>
                            </span>

                            <div>
                                <div class="fw-semibold">
                                    Document disponible
                                </div>

                                <div class="small text-secondary">
                                    {{ $registre->type_document }}
                                </div>
                            </div>

                        </div>

                        <a
                            href="{{ route(
                                'commercial.clients.validation.registre',
                                $client
                            ) }}"
                            target="_blank"
                            rel="noopener"
                            class="btn btn-soft"
                        >
                            <i class="bi bi-eye me-2"></i>
                            Consulter
                        </a>
                    </div>

                @else

                    <div class="alert alert-warning mb-0">
                        Aucun registre de commerce
                        n'a été retrouvé sur ce dossier.
                    </div>

                @endif


                @if (
                    $client->statut_validation_compte
                    === 'refuse'
                    && $client->motif_refus_validation
                )
                    <div class="alert alert-danger mt-4 mb-0">
                        <strong>Motif du dernier refus :</strong>
                        <br>
                        {{ $client->motif_refus_validation }}
                    </div>
                @endif

            </div>

        </div>

    </div>


    <div class="col-lg-5">

        <div class="app-card">

            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        Décision
                    </h2>

                    <p class="section-subtitle">
                        Validation du compte client
                    </p>
                </div>
            </div>


            <div class="section-body">

                @if (
                    $client->statut_validation_compte
                    === 'actif'
                )

                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i>

                        Ce compte est actif.
                    </div>

                @else

                    <div class="action-panel">

                        <div class="d-flex gap-3 mb-3">
                            <span class="item-icon">
                                <i class="bi bi-check-lg"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    Valider le compte
                                </div>

                                <div class="small text-secondary">
                                    Le client pourra accéder
                                    à l'ensemble de son espace.
                                </div>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route(
                                'commercial.clients.validation.approve',
                                $client
                            ) }}"
                            onsubmit="return confirm(
                                'Valider ce registre de commerce et activer le compte client ?'
                            );"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="btn btn-djezzy w-100"
                                {{ $registre ? '' : 'disabled' }}
                            >
                                <i class="bi bi-check-lg me-2"></i>
                                Valider le compte
                            </button>
                        </form>

                    </div>


                    <div class="action-panel">

                        <div class="d-flex gap-3 mb-3">
                            <span class="item-icon">
                                <i class="bi bi-x-lg"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    Refuser le registre
                                </div>

                                <div class="small text-secondary">
                                    Le client verra le motif
                                    et pourra remplacer son document.
                                </div>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route(
                                'commercial.clients.validation.reject',
                                $client
                            ) }}"
                        >
                            @csrf

                            <label
                                for="motif_refus_validation"
                                class="form-label"
                            >
                                Motif du refus
                            </label>

                            <textarea
                                id="motif_refus_validation"
                                name="motif_refus_validation"
                                class="form-control
                                    @error('motif_refus_validation')
                                        is-invalid
                                    @enderror"
                                placeholder="Expliquez la raison du refus..."
                                required
                            >{{ old('motif_refus_validation') }}</textarea>

                            @error('motif_refus_validation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <button
                                type="submit"
                                class="btn btn-outline-danger w-100 mt-3"
                                {{ $registre ? '' : 'disabled' }}
                            >
                                <i class="bi bi-x-lg me-2"></i>
                                Refuser le registre
                            </button>
                        </form>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection
