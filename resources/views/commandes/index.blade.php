@extends('layouts.app')

@section('title', 'Mes commandes')
@section('breadcrumb', 'Espace Client / Mes commandes')

@section('content')
    @php
        // Ces compteurs reflètent les statuts déjà fournis par les commandes.
        $total = $commandes->count();
        $soumises = $commandes->where('statut', 'soumise')->count();
        $attenteStock = $commandes->where('statut', 'en_attente_stock')->count();
        $validees = $commandes->where('statut', 'validee')->count();
    @endphp

    <section class="listing-hero" aria-labelledby="commandes-page-title">
        <div>
            <div class="page-eyebrow">Espace Client · Commandes</div>
            <h1 id="commandes-page-title" class="page-title">Mes commandes</h1>
            <p class="page-subtitle">Consultez vos commandes de quotas et suivez leur état.</p>
        </div>
        <div class="listing-actions">
            <div class="listing-count"><strong>{{ $total }}</strong><span>{{ $total > 1 ? 'commandes' : 'commande' }} au total</span></div>
            <a href="{{ route('commandes.create') }}" class="btn btn-djezzy px-3 py-2"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nouvelle commande</a>
        </div>
    </section>

    @if (session('error'))
        <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>{{ session('error') }}</div>
    @endif

    {{-- Même présentation des indicateurs que dans l'espace commercial. --}}
    <div class="row g-3 mb-4" aria-label="Résumé de vos commandes">
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Total</div><div class="stat-number">{{ $total }}</div></div><span class="stat-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span></div></div>
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Soumises</div><div class="stat-number">{{ $soumises }}</div></div><span class="stat-icon"><i class="bi bi-send-check" aria-hidden="true"></i></span></div></div>
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Attente stock</div><div class="stat-number">{{ $attenteStock }}</div></div><span class="stat-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span></div></div>
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Validées</div><div class="stat-number">{{ $validees }}</div></div><span class="stat-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span></div></div>
    </div>

    <section class="app-card overflow-hidden" aria-labelledby="commandes-list-title">
        <div class="section-header">
            <div><h2 id="commandes-list-title" class="section-title">Liste des commandes</h2><p class="section-subtitle">Les demandes associées à vos boutiques.</p></div>
            <span class="item-icon" aria-hidden="true"><i class="bi bi-bag-check"></i></span>
        </div>

        @if ($commandes->isEmpty())
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true"><i class="bi bi-bag"></i></div>
                <h3>Aucune commande</h3>
                <p class="mb-3">Vous n'avez encore effectué aucune commande.</p>
                <a href="{{ route('commandes.create') }}" class="btn btn-djezzy">Créer une commande</a>
            </div>
        @else
            <div class="table-responsive"><table class="table app-table responsive-table mb-0">
                <thead><tr><th scope="col">Commande</th><th scope="col">Boutique</th><th scope="col">Wilaya</th><th scope="col">Offres</th><th scope="col">Statut</th><th scope="col">Stock</th><th scope="col">Date</th><th scope="col" class="text-end">Action</th></tr></thead>
                <tbody>
                    @foreach ($commandes as $commande)
                        @php
                            $statutClass = match ($commande->statut) {
                                'soumise' => 'status-submitted',
                                'en_attente_stock' => 'status-wait',
                                'validee' => 'status-done',
                                'refusee' => 'status-refused',
                                default => 'status-other',
                            };
                            $statutLabel = match ($commande->statut) {
                                'soumise' => 'Soumise',
                                'en_attente_stock' => 'En attente de stock',
                                'validee' => 'Validée',
                                'refusee' => 'Refusée',
                                default => ucfirst($commande->statut),
                            };
                        @endphp
                        <tr class="data-row">
                            <td class="primary-cell" data-label="Commande">
                                <div class="item-identity"><span class="item-icon" aria-hidden="true"><i class="bi bi-bag"></i></span><span><span class="item-name d-block">Commande #{{ $commande->id_commande }}</span><span class="item-caption">{{ $commande->created_at?->format('d/m/Y') }}</span></span></div>
                            </td>
                            <td data-label="Boutique"><span class="text-value">{{ $commande->boutique->nom_boutique }}</span></td>
                            <td data-label="Wilaya">{{ $commande->boutique->wilaya }}</td>
                            <td data-label="Offres"><span class="category-badge">{{ $commande->lignes->count() }} offre{{ $commande->lignes->count() > 1 ? 's' : '' }}</span></td>
                            <td data-label="Statut"><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></td>
                            <td data-label="Stock">
                                @if ($commande->stock_reserve)
                                    <span class="small fw-semibold text-success"><i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>Réservé</span>
                                @else
                                    <span class="small fw-semibold text-secondary"><i class="bi bi-clock me-1" aria-hidden="true"></i>Non réservé</span>
                                @endif
                            </td>
                            <td data-label="Date" class="muted-value">{{ $commande->created_at?->format('d/m/Y') }}</td>
                            <td class="actions-cell text-end" data-label="Action"><div class="d-flex justify-content-end"><a href="{{ route('commandes.show', $commande->id_commande) }}" class="btn btn-sm btn-soft" aria-label="Voir la commande numéro {{ $commande->id_commande }}"><i class="bi bi-eye me-1" aria-hidden="true"></i>Voir</a></div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </section>
@endsection
