@extends('layouts.app')

@section('title', 'Validation boutique')
@section('breadcrumb', 'Espace Commercial / Validation boutiques / Détail')

@section('content')

<a
    href="{{ route('commercial.boutiques.validation.index') }}"
    class="back-link"
>
    <i class="bi bi-arrow-left"></i>
    Validation des boutiques
</a>


<div class="page-heading">

    <div>
        <div class="page-eyebrow">
            Dossier boutique
        </div>

        <h1 class="page-title">
            {{ $boutique->nom_boutique }}
        </h1>

        <p class="page-subtitle">
            Vérification du justificatif avant utilisation dans une commande.
        </p>
    </div>

    <div>
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
                        Informations de la boutique
                    </h2>

                    <p class="section-subtitle">
                        Boutique, propriétaire et justificatif
                    </p>
                </div>

                <span class="item-icon">
                    <i class="bi bi-shop"></i>
                </span>
            </div>


            <div class="section-body">

                <dl class="detail-grid mb-4">

                    <div class="detail-field">
                        <dt>Identifiant</dt>
                        <dd>#{{ $boutique->id_boutique }}</dd>
                    </div>

                    <div class="detail-field">
                        <dt>Client</dt>
                        <dd>
                            {{ optional($boutique->client)->name ?? '—' }}
                        </dd>
                    </div>

                    <div class="detail-field">
                        <dt>Wilaya</dt>
                        <dd>{{ $boutique->wilaya }}</dd>
                    </div>

                    <div class="detail-field">
                        <dt>Adresse</dt>
                        <dd>{{ $boutique->adresse }}</dd>
                    </div>

                    <div class="detail-field">
                        <dt>Cycle de vie</dt>
                        <dd>
                            {{ ucfirst($boutique->statut ?? 'active') }}
                        </dd>
                    </div>

                    <div class="detail-field">
                        <dt>Validation du justificatif</dt>
                        <dd>
                            @if ($boutique->statut_validation_justificatif === 'validee')
                                Validée
                            @elseif ($boutique->statut_validation_justificatif === 'refusee')
                                Refusée
                            @else
                                En attente
                            @endif
                        </dd>
                    </div>

                </dl>


                <h3 class="h6 fw-bold mb-3">
                    Justificatif de la boutique
                </h3>

                @if ($justificatif)

                    <div
                        class="d-flex align-items-center justify-content-between
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
                                    {{ $justificatif->type_document }}
                                </div>
                            </div>
                        </div>

                        <a
                            href="{{ route('commercial.boutiques.validation.document', $boutique) }}"
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
                        Aucun justificatif n'a été retrouvé pour cette boutique.
                    </div>

                @endif


                @if (
                    $boutique->statut_validation_justificatif === 'refusee'
                    && $boutique->motif_refus_justificatif
                )
                    <div class="alert alert-danger mt-4 mb-0">
                        <strong>Motif du dernier refus :</strong><br>
                        {{ $boutique->motif_refus_justificatif }}
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
                        Validation du justificatif
                    </p>
                </div>
            </div>


            <div class="section-body">

                @if ($boutique->statut_validation_justificatif === 'validee')

                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        Cette boutique est validée.
                    </div>

                @else

                    <div class="action-panel">

                        <div class="d-flex gap-3 mb-3">
                            <span class="item-icon">
                                <i class="bi bi-check-lg"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    Valider la boutique
                                </div>

                                <div class="small text-secondary">
                                    Elle pourra être utilisée pour une nouvelle commande.
                                </div>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('commercial.boutiques.validation.approve', $boutique) }}"
                            onsubmit="return confirm('Valider ce justificatif ?');"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="btn btn-djezzy w-100"
                                {{ $justificatif ? '' : 'disabled' }}
                            >
                                <i class="bi bi-check-lg me-2"></i>
                                Valider la boutique
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
                                    Refuser le justificatif
                                </div>

                                <div class="small text-secondary">
                                    Le client verra le motif et pourra remplacer le document.
                                </div>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('commercial.boutiques.validation.reject', $boutique) }}"
                        >
                            @csrf

                            <label
                                for="motif_refus_justificatif"
                                class="form-label"
                            >
                                Motif du refus
                            </label>

                            <textarea
                                id="motif_refus_justificatif"
                                name="motif_refus_justificatif"
                                class="form-control @error('motif_refus_justificatif') is-invalid @enderror"
                                placeholder="Expliquez la raison du refus..."
                                required
                            >{{ old('motif_refus_justificatif') }}</textarea>

                            @error('motif_refus_justificatif')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <button
                                type="submit"
                                class="btn btn-outline-danger w-100 mt-3"
                                {{ $justificatif ? '' : 'disabled' }}
                            >
                                <i class="bi bi-x-lg me-2"></i>
                                Refuser le justificatif
                            </button>
                        </form>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection
