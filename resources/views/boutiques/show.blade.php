@extends('layouts.app')
@section('title', $boutique->nom_boutique)
@section('breadcrumb', 'Mes boutiques / Détail')
@section('content')
<div class="page-heading">
    <div>
        <div class="page-eyebrow">Mes boutiques</div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <h1 class="page-title mb-0">{{ $boutique->nom_boutique }}</h1>
            @if ($boutique->statut === 'active')<span class="status-pill status-done">Active</span>@else<span class="status-pill status-wait">Archivée</span>@endif
        </div>
        <p class="page-subtitle mt-2">Informations de la boutique sélectionnée.</p>
    </div>
    <div class="page-heading-actions">
        <a href="{{ route('boutiques.index') }}" class="btn btn-soft px-4"><i class="bi bi-arrow-left me-2"></i>Retour</a>
        <a href="{{ route('boutiques.edit',$boutique) }}" class="btn btn-djezzy px-4"><i class="bi bi-pencil me-2"></i>Modifier</a>
    </div>
</div>
@if ($boutique->statut === 'archivee')
<div class="note-panel mb-4"><i class="bi bi-archive me-2"></i>Cette boutique est archivée. Elle reste disponible dans votre historique, mais elle ne peut pas être utilisée pour une nouvelle commande.</div>
@endif
<div class="row g-4">
    <div class="col-lg-8"><div class="app-card">
        <div class="section-header"><div><h2 class="section-title">Détail de la boutique</h2><p class="section-subtitle">Référence #{{ $boutique->id_boutique }}</p></div><span class="item-icon"><i class="bi bi-shop"></i></span></div>
        <div class="section-body"><dl class="detail-grid mb-0">
            <div class="detail-field"><dt>Identifiant</dt><dd>#{{ $boutique->id_boutique }}</dd></div>
            <div class="detail-field"><dt>Nom</dt><dd>{{ $boutique->nom_boutique }}</dd></div>
            <div class="detail-field"><dt>Wilaya</dt><dd><i class="bi bi-geo-alt text-danger me-1"></i>{{ $boutique->wilaya }}</dd></div>
            <div class="detail-field"><dt>Adresse</dt><dd>{{ $boutique->adresse }}</dd></div>
        </dl></div>
    </div></div>
    <div class="col-lg-4"><div class="app-card h-100">
        <div class="section-header"><div><h2 class="section-title">Justificatif du local</h2><p class="section-subtitle">Document associé à cette boutique</p></div></div>
        <div class="section-body">
        @if ($justificatif)
            <div class="text-center py-3"><div class="empty-icon mb-3"><i class="bi bi-file-earmark-check"></i></div><h3 class="fs-6 fw-bold">Document enregistré</h3><p class="text-secondary small mb-4">Le justificatif du local est disponible.</p><a href="{{ route('boutiques.document',[$boutique,$justificatif]) }}" target="_blank" rel="noopener" class="btn btn-djezzy w-100"><i class="bi bi-eye me-2"></i>Consulter le document</a></div>
        @else
            <div class="empty-state py-4"><div class="empty-icon"><i class="bi bi-file-earmark-x"></i></div><h3>Aucun justificatif</h3><p>Aucun document n'est associé à cette boutique.</p></div>
        @endif
        </div>
    </div></div>
</div>
@if ((int)$boutique->commandes_count > 0)
<div class="note-panel mt-4"><i class="bi bi-info-circle me-2"></i>Cette boutique est liée à {{ $boutique->commandes_count }} {{ $boutique->commandes_count > 1 ? 'commandes' : 'commande' }}. Elle reste conservée dans l'historique.</div>
@endif

@include('boutiques.partials.validation-justificatif')

@endsection
