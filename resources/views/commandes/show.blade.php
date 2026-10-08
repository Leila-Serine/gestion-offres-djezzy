@extends('layouts.app')

@section('title', 'Commande #' . $commande->id_commande)
@section('breadcrumb', 'Espace Client / Mes commandes / Commande #' . $commande->id_commande)

@section('content')
    @php
        // L'interface interprète uniquement les statuts déjà transmis par la commande.
        $statut = match ($commande->statut) {
            'soumise' => [
                'label' => 'Soumise',
                'class' => 'order-status-banner--submitted',
                'icon' => 'bi-send-check',
                'message' => 'Votre commande a été soumise et attend le traitement du commercial.',
            ],
            'en_attente_stock' => [
                'label' => 'En attente de stock',
                'class' => 'order-status-banner--waiting',
                'icon' => 'bi-hourglass-split',
                'message' => 'La disponibilité actuelle ne permet pas encore de traiter complètement cette commande.',
            ],
            'validee' => [
                'label' => 'Validée',
                'class' => 'order-status-banner--done',
                'icon' => 'bi-check-circle',
                'message' => 'Votre commande a été validée par le commercial.',
            ],
            'refusee' => [
                'label' => 'Refusée',
                'class' => 'order-status-banner--refused',
                'icon' => 'bi-x-circle',
                'message' => 'Votre commande a été refusée.',
            ],
            default => [
                'label' => ucfirst($commande->statut),
                'class' => 'order-status-banner--submitted',
                'icon' => 'bi-info-circle',
                'message' => 'Consultez les informations de votre commande.',
            ],
        };
    @endphp

    <a href="{{ route('commandes.index') }}" class="back-link"><i class="bi bi-arrow-left" aria-hidden="true"></i>Mes commandes</a>

    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Le layout fournit déjà le conteneur et affiche le message session('success'). --}}
    <div class="page-heading">
        <div>
            <div class="page-eyebrow">Espace Client · Suivi de commande</div>
            <h1 class="page-title">Commande #{{ $commande->id_commande }}</h1>
            <p class="page-subtitle">Créée le {{ $commande->created_at?->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="invoice-total-box"><span>Montant total</span><strong>{{ number_format((float) $commande->montant_total, 2, ',', ' ') }} DA</strong></div>
    </div>

    <div class="order-status-banner {{ $statut['class'] }} mb-4" role="status">
        <span class="order-status-icon"><i class="bi {{ $statut['icon'] }}" aria-hidden="true"></i></span>
        <div>
            <strong>{{ $statut['label'] }}</strong>
            <p>{{ $statut['message'] }}</p>
            @if ($commande->motif_statut)
                <p class="mb-0"><strong>Information :</strong> {{ $commande->motif_statut }}</p>
            @endif
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="app-card overflow-hidden" aria-labelledby="facture-title">
                <div class="section-header">
                    <div><h2 class="section-title" id="facture-title">Facture</h2><p class="section-subtitle">Détail des offres, quantités et montants de la commande.</p></div>
                    <span class="invoice-title-icon" aria-hidden="true"><i class="bi bi-receipt"></i></span>
                </div>
                {{-- Les montants affichés proviennent des lignes enregistrées, sans recalcul du prix actuel de l'offre. --}}
                <div class="table-responsive">
                    <table class="table app-table invoice-table mb-0">
                        <thead><tr><th scope="col">Offre</th><th scope="col" class="text-center">Quantité</th><th scope="col" class="text-end">Prix unitaire</th><th scope="col" class="text-end">Montant</th></tr></thead>
                        <tbody>
                            @foreach ($commande->lignes as $ligne)
                                <tr>
                                    <td><span class="item-name d-block">{{ $ligne->offre->nom_offre }}</span><span class="category-badge">{{ $ligne->offre->categorie }}</span></td>
                                    <td class="text-center text-value">{{ $ligne->quantite }}</td>
                                    <td class="text-end money">{{ number_format((float) $ligne->prix_unitaire, 2, ',', ' ') }} DA</td>
                                    <td class="text-end money text-value">{{ number_format((float) $ligne->montant_ligne, 2, ',', ' ') }} DA</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><td colspan="3" class="text-end fw-bold">Montant total</td><td class="text-end money"><strong>{{ number_format((float) $commande->montant_total, 2, ',', ' ') }} DA</strong></td></tr></tfoot>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="app-card" aria-labelledby="infos-title">
                <div class="section-header"><div><h2 class="section-title" id="infos-title">Informations</h2><p class="section-subtitle">Boutique et état des quotas</p></div></div>
                <div class="section-body detail-stack"><dl class="mb-0">
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
                    <div class="detail-field"><dt>Nombre d'offres</dt><dd>{{ $commande->lignes->count() }}</dd></div>
                    <div class="detail-field"><dt>Référence</dt><dd>#{{ $commande->id_commande }}</dd></div>
                </dl></div>
            </section>
        </div>
    </div>
@endsection
