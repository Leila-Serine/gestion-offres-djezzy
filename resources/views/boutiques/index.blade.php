@extends('layouts.app')
@section('title', 'Mes boutiques')
@section('breadcrumb', 'Mes boutiques')
@section('content')
<div class="listing-hero">
    <div>
        <div class="page-eyebrow">Espace client</div>
        <h1 class="page-title">Mes boutiques</h1>
        <p class="page-subtitle">Consultez et gérez les boutiques associées à votre compte.</p>
    </div>
    <div class="listing-actions">
        <div class="listing-count"><strong>{{ $boutiques->count() }}</strong><span>boutiques au total</span></div>
        <a href="{{ route('boutiques.create') }}" class="btn btn-djezzy px-4"><i class="bi bi-plus-lg me-2"></i>Ajouter une boutique</a>
    </div>
</div>
<div class="app-card overflow-hidden">
    <div class="section-header">
        <div><h2 class="section-title">Liste des boutiques</h2><p class="section-subtitle">Vos points de vente enregistrés</p></div>
        <span class="item-icon"><i class="bi bi-shop"></i></span>
    </div>
    <div class="table-responsive">
        <table class="table app-table responsive-table mb-0">
            <thead><tr><th>Identifiant</th><th>Nom</th><th>Wilaya</th><th>Adresse</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($boutiques as $boutique)
                <tr class="data-row">
                    <td data-label="Identifiant"><span class="text-value">#{{ $boutique->id_boutique }}</span></td>
                    <td data-label="Nom" class="primary-cell">
                        <div class="item-identity"><span class="item-icon"><i class="bi bi-shop"></i></span><div><div class="item-name">{{ $boutique->nom_boutique }}</div><div class="item-caption">Boutique #{{ $boutique->id_boutique }}</div></div></div>
                    </td>
                    <td data-label="Wilaya">{{ $boutique->wilaya }}</td>
                    <td data-label="Adresse">{{ $boutique->adresse }}</td>
                    <td data-label="Statut">
                        @if ($boutique->statut === 'active')
                            <span class="status-pill status-done">Active</span>
                        @else
                            <span class="status-pill status-wait">Archivée</span>
                        @endif
                    </td>
                    <td data-label="Actions" class="actions-cell text-end">
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                            <a href="{{ route('boutiques.show',$boutique) }}" class="btn btn-soft btn-sm"><i class="bi bi-eye me-1"></i>Voir</a>
                            <a href="{{ route('boutiques.edit',$boutique) }}" class="btn btn-outline-djezzy btn-sm"><i class="bi bi-pencil me-1"></i>Modifier</a>
                            @if ($boutique->statut === 'active')
                                <form method="POST" action="{{ route('boutiques.update',$boutique) }}" onsubmit="return confirm('Archiver cette boutique ? Elle ne pourra plus être utilisée pour une nouvelle commande.');">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action_statut" value="archiver">
                                    <button type="submit" class="btn btn-sm" style="border:1px solid #d99a00;color:#8a5a00;background:#fff8e6;font-weight:600;"><i class="bi bi-archive me-1"></i>Archiver</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('boutiques.update',$boutique) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action_statut" value="reactiver">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i>Réactiver</button>
                                </form>
                            @endif
                            @if ((int)$boutique->commandes_count === 0)
                                <form method="POST" action="{{ route('boutiques.destroy',$boutique) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer définitivement cette boutique ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6" class="text-center"><div class="empty-state"><div class="empty-icon"><i class="bi bi-shop"></i></div><h3>Aucune boutique</h3><p>Ajoutez votre première boutique pour commencer.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
