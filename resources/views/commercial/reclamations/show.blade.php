@extends('layouts.app')

@section('title', 'Réclamation client')
@section('breadcrumb', 'Espace Commercial / Réclamations / Détail')

@section('content')
    @php
        $statutClass = match ($reclamation->statut) {
            'ouverte' => 'status-open', 'en_cours' => 'status-progress', 'resolue' => 'status-done', default => 'status-other',
        };
        $statutLabel = match ($reclamation->statut) {
            'ouverte' => 'Ouverte', 'en_cours' => 'En cours', 'resolue' => 'Résolue', default => ucfirst($reclamation->statut),
        };
    @endphp

    <a class="back-link" href="{{ route('commercial.reclamations.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i>Réclamations</a>
    @if (session('error')) <div class="alert alert-danger" role="alert">{{ session('error') }}</div> @endif

    <div class="page-heading"><div><div class="page-eyebrow">Espace Commercial · Assistance</div><h1 class="page-title">Réclamation #{{ $reclamation->id_reclamation }}</h1><p class="page-subtitle">{{ $reclamation->objet }}</p></div><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-4">
            <section class="app-card" aria-labelledby="reclamation-info-title"><div class="section-header"><div><h2 id="reclamation-info-title" class="section-title">Informations</h2><p class="section-subtitle">Détails de la demande</p></div></div><div class="section-body detail-stack"><dl class="mb-0">
                <div class="detail-field"><dt>Référence</dt><dd>#{{ $reclamation->id_reclamation }}</dd></div>
                <div class="detail-field"><dt>Client</dt><dd>{{ $reclamation->client->name }}<span class="muted-value d-block mt-1">{{ $reclamation->client->email }}</span></dd></div>
                <div class="detail-field"><dt>Objet</dt><dd>{{ $reclamation->objet }}</dd></div>
                <div class="detail-field"><dt>Statut</dt><dd><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></dd></div>
            </dl></div></section>
        </div>

        <div class="col-lg-8">
            <section class="app-card overflow-hidden" aria-labelledby="conversation-title">
                <div class="section-header"><div><h2 id="conversation-title" class="section-title">Conversation</h2><p class="section-subtitle">Échanges avec le client au sujet de sa demande.</p></div><span class="item-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span></div>
                <div class="conversation-body">
                    {{-- Le message initial du client ouvre la conversation. --}}
                    <div class="message-row"><div class="message-bubble"><div class="message-meta">{{ $reclamation->client->name }} · {{ $reclamation->created_at?->format('d/m/Y H:i') }}</div>{{ $reclamation->message }}</div></div>
                    @foreach ($reclamation->messages as $message)
                        @php $isCommercial = $message->auteur->role === 'commercial'; @endphp
                        <div class="message-row {{ $isCommercial ? 'mine' : '' }}"><div class="message-bubble"><div class="message-meta">{{ $isCommercial ? 'Vous' : $message->auteur->name }} · {{ $message->created_at?->format('d/m/Y H:i') }}</div>{{ $message->contenu }}</div></div>
                    @endforeach
                </div>

                @if ($reclamation->statut !== 'resolue')
                    <div class="conversation-composer">
                        {{-- Répondre et résoudre sont deux actions POST distinctes. --}}
                        <form method="POST" action="{{ route('commercial.reclamations.messages.store', $reclamation->id_reclamation) }}">
                            @csrf
                            <label for="contenu" class="form-label">Répondre au client</label>
                            <textarea id="contenu" name="contenu" rows="4" class="form-control @error('contenu') is-invalid @enderror" placeholder="Écrivez votre réponse..." required>{{ old('contenu') }}</textarea>
                            @error('contenu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="text-end mt-3"><button type="submit" class="btn btn-djezzy px-4"><i class="bi bi-send me-2" aria-hidden="true"></i>Envoyer</button></div>
                        </form>
                        <div class="border-top mt-4 pt-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div><strong>Réclamation traitée ?</strong><p class="section-subtitle mb-0">Marquez-la comme résolue lorsque le traitement est terminé.</p></div>
                            <form method="POST" action="{{ route('commercial.reclamations.resolve', $reclamation->id_reclamation) }}">@csrf<button type="submit" class="btn btn-outline-djezzy"><i class="bi bi-check-circle me-2" aria-hidden="true"></i>Marquer comme résolue</button></form>
                        </div>
                    </div>
                @else
                    <div class="conversation-composer"><div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2" aria-hidden="true"></i>Cette réclamation est résolue.</div></div>
                @endif
            </section>
        </div>
    </div>
@endsection
