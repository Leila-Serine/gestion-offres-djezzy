@extends('layouts.app')

@section('title', 'Réclamations clients')
@section('breadcrumb', 'Espace Commercial / Réclamations')

@section('content')
    <div class="listing-hero"><div><div class="page-eyebrow">Espace Commercial · Assistance</div><h1 class="page-title">Réclamations clients</h1><p class="page-subtitle">Consultez et traitez les demandes envoyées par les clients.</p></div><div class="listing-count"><strong>{{ $reclamations->count() }}</strong><span>Réclamations</span></div></div>

    <section class="app-card overflow-hidden" aria-labelledby="reclamations-title">
        <div class="section-header"><div><h2 id="reclamations-title" class="section-title">Demandes reçues</h2><p class="section-subtitle">Retrouvez le client, l'objet et l'état de chaque réclamation.</p></div></div>
        @if ($reclamations->isEmpty())
            <div class="empty-state"><div class="empty-icon"><i class="bi bi-chat-square-text" aria-hidden="true"></i></div><h3>Aucune réclamation</h3><p>Aucune réclamation client à afficher.</p></div>
        @else
            <div class="table-responsive"><table class="table app-table responsive-table mb-0">
                <thead><tr><th>Réclamation</th><th>Client</th><th>Statut</th><th>Date</th><th class="text-end">Action</th></tr></thead><tbody>
                    @foreach ($reclamations as $reclamation)
                        @php
                            $statutClass = match ($reclamation->statut) {
                                'ouverte' => 'status-open', 'en_cours' => 'status-progress',
                                'resolue' => 'status-done', default => 'status-other',
                            };
                            $statutLabel = match ($reclamation->statut) {
                                'ouverte' => 'Ouverte', 'en_cours' => 'En cours',
                                'resolue' => 'Résolue', default => ucfirst($reclamation->statut),
                            };
                        @endphp
                        <tr class="data-row">
                            <td class="primary-cell" data-label="Objet"><div class="item-identity"><span class="item-icon"><i class="bi bi-chat-square-text" aria-hidden="true"></i></span><span><span class="item-name d-block">{{ $reclamation->objet }}</span><span class="item-caption">Réclamation #{{ $reclamation->id_reclamation }}</span></span></div></td>
                            <td data-label="Client"><span class="text-value">{{ $reclamation->client->name }}</span></td>
                            <td data-label="Statut"><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></td>
                            <td data-label="Date" class="muted-value">{{ $reclamation->created_at?->format('d/m/Y') }}</td>
                            <td class="actions-cell text-end" data-label="Action"><div class="d-flex justify-content-end"><a href="{{ route('commercial.reclamations.show', $reclamation->id_reclamation) }}" class="btn btn-sm btn-soft"><i class="bi bi-eye me-1" aria-hidden="true"></i>Examiner</a></div></td>
                        </tr>
                    @endforeach
                </tbody></table></div>
        @endif
    </section>
@endsection
