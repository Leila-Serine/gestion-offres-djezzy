@extends('layouts.app')

@section('title', 'Mes réclamations')
@section('breadcrumb', 'Espace Client / Mes réclamations')

@section('content')
    {{-- L'en-tête reprend celui des listes d'offres et de boutiques. --}}
    <div class="listing-hero">
        <div>
            <div class="page-eyebrow">Espace Client · Assistance</div>
            <h1 class="page-title">Mes réclamations</h1>
            <p class="page-subtitle">Consultez et suivez vos demandes envoyées au service commercial.</p>
        </div>
        <div class="listing-actions">
            <div class="listing-count"><strong>{{ $reclamations->count() }}</strong><span>Réclamations</span></div>
            <a href="{{ route('reclamations.create') }}" class="btn btn-djezzy px-3 py-2"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Nouvelle réclamation</a>
        </div>
    </div>

    <section class="app-card overflow-hidden" aria-labelledby="reclamations-title">
        <div class="section-header">
            <div><h2 id="reclamations-title" class="section-title">Vos demandes</h2><p class="section-subtitle">Retrouvez leur objet, leur statut et leur date de création.</p></div>
        </div>

        @if ($reclamations->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-chat-left-text" aria-hidden="true"></i></div>
                <h3>Aucune réclamation</h3>
                <p class="mb-3">Vous n'avez encore envoyé aucune réclamation.</p>
                <a href="{{ route('reclamations.create') }}" class="btn btn-djezzy">Créer une réclamation</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table app-table responsive-table mb-0">
                    <thead><tr><th>Réclamation</th><th>Statut</th><th>Date</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        @foreach ($reclamations as $reclamation)
                            @php
                                $statutClass = match ($reclamation->statut) {
                                    'ouverte' => 'status-open',
                                    'en_cours' => 'status-progress',
                                    'resolue' => 'status-done',
                                    default => 'status-other',
                                };
                                $statutLabel = match ($reclamation->statut) {
                                    'ouverte' => 'Ouverte',
                                    'en_cours' => 'En cours',
                                    'resolue' => 'Résolue',
                                    default => ucfirst($reclamation->statut),
                                };
                            @endphp
                            <tr class="data-row">
                                <td class="primary-cell" data-label="Objet"><div class="item-identity"><span class="item-icon"><i class="bi bi-chat-square-text" aria-hidden="true"></i></span><span><span class="item-name d-block">{{ $reclamation->objet }}</span><span class="item-caption">Réclamation #{{ $reclamation->id_reclamation }}</span></span></div></td>
                                <td data-label="Statut"><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></td>
                                <td data-label="Date" class="muted-value">{{ $reclamation->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="actions-cell text-end" data-label="Action"><div class="d-flex justify-content-end"><a href="{{ route('reclamations.show', $reclamation->id_reclamation) }}" class="btn btn-sm btn-soft"><i class="bi bi-eye me-1" aria-hidden="true"></i>Consulter</a></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
