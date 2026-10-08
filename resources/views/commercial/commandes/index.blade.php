@extends('layouts.app')

@section('title', 'Commandes clients')
@section('breadcrumb', 'Espace Commercial / Commandes clients')

@section('content')
    @php
        // Les compteurs reprennent exactement les statuts présents dans la vue fournie.
        $total = $commandes->count();
        $soumises = $commandes->where('statut', 'soumise')->count();
        $attente = $commandes->where('statut', 'en_attente_stock')->count();
        $validees = $commandes->where('statut', 'validee')->count();
    @endphp

    <div class="listing-hero">
        <div><div class="page-eyebrow">Espace Commercial · Suivi des demandes</div><h1 class="page-title">Commandes clients</h1><p class="page-subtitle">Consultez les demandes de quotas envoyées par les clients.</p></div>
        <div class="listing-count"><strong>{{ $total }}</strong><span>Commandes</span></div>
    </div>

    <div class="row g-3 mb-4" aria-label="Résumé des commandes">
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Total</div><div class="stat-number">{{ $total }}</div></div><span class="stat-icon"><i class="bi bi-collection" aria-hidden="true"></i></span></div></div>
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Soumises</div><div class="stat-number">{{ $soumises }}</div></div><span class="stat-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span></div></div>
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">En attente de stock</div><div class="stat-number">{{ $attente }}</div></div><span class="stat-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span></div></div>
        <div class="col-sm-6 col-xl-3"><div class="stat-tile"><div><div class="stat-label">Validées</div><div class="stat-number">{{ $validees }}</div></div><span class="stat-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span></div></div>
    </div>

    <section class="app-card overflow-hidden" aria-labelledby="commandes-title">
        <div class="section-header"><div><h2 id="commandes-title" class="section-title">Liste des commandes</h2><p class="section-subtitle">Client, boutique, wilaya et état de chaque demande.</p></div></div>
        @if ($commandes->isEmpty())
            <div class="empty-state"><div class="empty-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></div><h3>Aucune commande</h3><p>Aucune commande client à afficher.</p></div>
        @else
            <div class="table-responsive">
                <table class="table app-table responsive-table mb-0">
                    <thead><tr><th>Commande</th><th>Client</th><th>Boutique</th><th>Wilaya</th><th>Statut</th><th>Date</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        @foreach ($commandes as $commande)
                            @php
                                $statutClass = match ($commande->statut) {
                                    'soumise' => 'status-submitted', 'en_attente_stock' => 'status-wait',
                                    'validee' => 'status-done', 'refusee' => 'status-refused', default => 'status-other',
                                };
                                $statutLabel = match ($commande->statut) {
                                    'soumise' => 'Soumise', 'en_attente_stock' => 'Attente stock',
                                    'validee' => 'Validée', 'refusee' => 'Refusée', default => ucfirst($commande->statut),
                                };
                            @endphp
                            <tr class="data-row">
                                <td class="primary-cell" data-label="Commande"><div class="item-identity"><span class="item-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span><span><span class="item-name d-block">Commande #{{ $commande->id_commande }}</span><span class="item-caption">{{ $commande->created_at?->format('d/m/Y') }}</span></span></div></td>
                                <td data-label="Client"><span class="text-value">{{ $commande->client->name }}</span></td>
                                <td data-label="Boutique">{{ $commande->boutique->nom_boutique }}</td>
                                <td data-label="Wilaya">{{ $commande->boutique->wilaya }}</td>
                                <td data-label="Statut"><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></td>
                                <td data-label="Date" class="muted-value">{{ $commande->created_at?->format('d/m/Y') }}</td>
                                <td class="actions-cell text-end" data-label="Action"><div class="d-flex justify-content-end"><a href="{{ route('commercial.commandes.show', $commande->id_commande) }}" class="btn btn-sm btn-soft"><i class="bi bi-eye me-1" aria-hidden="true"></i>Examiner</a></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
