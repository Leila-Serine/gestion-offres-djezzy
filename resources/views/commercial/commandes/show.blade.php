@extends('layouts.app')

@section('title', 'Commande client #' . $commande->id_commande)
@section('breadcrumb', 'Espace Commercial / Commandes clients / Commande #' . $commande->id_commande)

@section('content')
    @php
        $statut = match ($commande->statut) {
            'soumise' => ['label' => 'Soumise', 'class' => 'order-status-banner--submitted', 'icon' => 'bi-send-check'],
            'en_attente_stock' => ['label' => 'En attente de stock', 'class' => 'order-status-banner--waiting', 'icon' => 'bi-hourglass-split'],
            'validee' => ['label' => 'Validée', 'class' => 'order-status-banner--done', 'icon' => 'bi-check-circle'],
            'refusee' => ['label' => 'Refusée', 'class' => 'order-status-banner--refused', 'icon' => 'bi-x-circle'],
            default => ['label' => ucfirst($commande->statut), 'class' => 'order-status-banner--submitted', 'icon' => 'bi-info-circle'],
        };
    @endphp

    <a href="{{ route('commercial.commandes.index') }}" class="back-link"><i class="bi bi-arrow-left" aria-hidden="true"></i>Commandes clients</a>
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="page-heading">
        <div>
            <div class="page-eyebrow">Espace Commercial · Détail</div>
            <h1 class="page-title">Commande #{{ $commande->id_commande }}</h1>
            <p class="page-subtitle">{{ $commande->client->name }} · {{ $commande->created_at?->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="invoice-total-box"><span>Montant total</span><strong>{{ number_format((float) $commande->montant_total, 2, ',', ' ') }} DA</strong></div>
    </div>

    <div class="order-status-banner {{ $statut['class'] }} mb-4" role="status">
        <span class="order-status-icon"><i class="bi {{ $statut['icon'] }}" aria-hidden="true"></i></span>
        <div><strong>{{ $statut['label'] }}</strong>@if ($commande->motif_statut)<p class="mb-0">{{ $commande->motif_statut }}</p>@endif</div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="app-card overflow-hidden" aria-labelledby="detail-tarifaire-title">
                <div class="section-header">
                    <div><h2 class="section-title" id="detail-tarifaire-title">Détail de la commande</h2><p class="section-subtitle">Quantités et montants enregistrés au moment de la commande.</p></div>
                    <span class="invoice-title-icon" aria-hidden="true"><i class="bi bi-receipt"></i></span>
                </div>
                {{-- Les prix de la ligne restent ceux appliqués à la commande, même si l'offre change ensuite. --}}
                <div class="table-responsive"><table class="table app-table invoice-table mb-0">
                    <thead><tr><th scope="col">Offre</th><th scope="col" class="text-center">Qté</th><th scope="col" class="text-end">Prix unitaire</th><th scope="col" class="text-end">Montant</th></tr></thead>
                    <tbody>
                        @foreach ($commande->lignes as $ligne)
                            <tr><td><span class="item-name d-block">{{ $ligne->offre->nom_offre }}</span><span class="category-badge">{{ $ligne->offre->categorie }}</span></td><td class="text-center text-value">{{ $ligne->quantite }}</td><td class="text-end money">{{ number_format((float) $ligne->prix_unitaire, 2, ',', ' ') }} DA</td><td class="text-end money text-value">{{ number_format((float) $ligne->montant_ligne, 2, ',', ' ') }} DA</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><td colspan="3" class="text-end fw-bold">Montant total</td><td class="text-end money"><strong>{{ number_format((float) $commande->montant_total, 2, ',', ' ') }} DA</strong></td></tr></tfoot>
                </table></div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="app-card mb-4" aria-labelledby="client-boutique-title">
                <div class="section-header"><div><h2 id="client-boutique-title" class="section-title">Client et boutique</h2><p class="section-subtitle">Informations liées à la commande</p></div></div>
                <div class="section-body detail-stack"><dl class="mb-0">
                    <div class="detail-field"><dt>Client</dt><dd>{{ $commande->client->name }}</dd></div>
                    <div class="detail-field"><dt>Email</dt><dd>{{ $commande->client->email }}</dd></div>
                    <div class="detail-field"><dt>Boutique</dt><dd>{{ $commande->boutique->nom_boutique }}</dd></div>
                    <div class="detail-field"><dt>Wilaya</dt><dd>{{ $commande->boutique->wilaya }}</dd></div>
                    <div class="detail-field"><dt>Adresse</dt><dd>{{ $commande->boutique->adresse }}</dd></div>
                    <div class="detail-field"><dt>Stock</dt><dd>
                        @if ($commande->stock_reserve)
                            <span class="status-pill status-done">Réservé</span>
                        @else
                            <span class="status-pill status-wait">Non réservé</span>
                        @endif
                    </dd></div>
                </dl></div>
            </section>

            {{-- Les formulaires et routes de traitement de la vue existante sont conservés. --}}
            @if (! in_array($commande->statut, ['validee', 'refusee']))
                <section class="app-card" aria-labelledby="traitement-title">
                    <div class="section-header"><div><h2 id="traitement-title" class="section-title">Traitement</h2><p class="section-subtitle">Décision sur cette commande</p></div></div>
                    <div class="section-body">
                        <div class="action-panel">
                            <div class="d-flex align-items-center gap-3 mb-3"><span class="item-icon"><i class="bi bi-check-lg" aria-hidden="true"></i></span><div><strong>Valider la commande</strong><div class="muted-value">Confirmer cette demande.</div></div></div>
                            <form method="POST" action="{{ route('commercial.commandes.validate', $commande->id_commande) }}">
                                @csrf
                                <button type="submit" class="btn btn-djezzy w-100"><i class="bi bi-check-lg me-2" aria-hidden="true"></i>Valider la commande</button>
                            </form>
                        </div>
                        <div class="action-panel">
                            <div class="d-flex align-items-center gap-3 mb-3"><span class="item-icon"><i class="bi bi-x-lg" aria-hidden="true"></i></span><div><strong>Refuser la commande</strong><div class="muted-value">Indiquez le motif du refus.</div></div></div>
                            <form method="POST" action="{{ route('commercial.commandes.refuse', $commande->id_commande) }}">
                                @csrf
                                <label for="motif" class="form-label">Motif du refus</label>
                                <textarea id="motif" name="motif" rows="3" class="form-control @error('motif') is-invalid @enderror" placeholder="Expliquez la raison du refus..." required>{{ old('motif') }}</textarea>
                                @error('motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <button type="submit" class="btn btn-outline-djezzy w-100 mt-3"><i class="bi bi-x-lg me-2" aria-hidden="true"></i>Refuser la commande</button>
                            </form>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
