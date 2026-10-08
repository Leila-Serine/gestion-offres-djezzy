@extends('layouts.app')

@section('title', 'Réclamation')
@section('breadcrumb', 'Espace Client / Réclamation')

@section('content')
    @php
        $statutClass = match ($reclamation->statut) {
            'ouverte' => 'status-open', 'en_cours' => 'status-progress', 'resolue' => 'status-done', default => 'status-other',
        };
        $statutLabel = match ($reclamation->statut) {
            'ouverte' => 'Ouverte', 'en_cours' => 'En cours', 'resolue' => 'Résolue', default => ucfirst($reclamation->statut),
        };
    @endphp

    <a class="back-link" href="{{ route('reclamations.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i>Mes réclamations</a>
    @if (session('error')) <div class="alert alert-danger" role="alert">{{ session('error') }}</div> @endif

    <div class="page-heading">
        <div><div class="page-eyebrow">Suivi de demande</div><h1 class="page-title">Réclamation #{{ $reclamation->id_reclamation }}</h1><p class="page-subtitle">Créée le {{ $reclamation->created_at?->format('d/m/Y à H:i') }}</p></div>
        <span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-4">
            <section class="app-card" aria-labelledby="reclamation-info-title">
                <div class="section-header"><div><h2 id="reclamation-info-title" class="section-title">Informations</h2><p class="section-subtitle">Détails de votre demande</p></div></div>
                <div class="section-body detail-stack">
                    <dl class="mb-0">
                        <div class="detail-field"><dt>Référence</dt><dd>#{{ $reclamation->id_reclamation }}</dd></div>
                        <div class="detail-field"><dt>Objet</dt><dd>{{ $reclamation->objet }}</dd></div>
                        <div class="detail-field"><dt>Statut</dt><dd><span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span></dd></div>
                        <div class="detail-field"><dt>Créée le</dt><dd>{{ $reclamation->created_at?->format('d/m/Y à H:i') }}</dd></div>
                    </dl>
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="app-card overflow-hidden" aria-labelledby="conversation-title">
                <div class="section-header"><div><h2 id="conversation-title" class="section-title">Conversation</h2><p class="section-subtitle">Suivez les échanges concernant votre réclamation.</p></div><span class="item-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span></div>
                <div class="conversation-body">
                    {{-- Le message initial est affiché à droite, comme les réponses du client. --}}
                    <div class="message-row mine"><div class="message-bubble"><div class="message-meta">Vous · {{ $reclamation->created_at?->format('d/m/Y H:i') }}</div>{{ $reclamation->message }}</div></div>
                    @foreach ($reclamation->messages as $message)
                        @php $isClient = $message->auteur->role === 'client'; @endphp
                        <div class="message-row {{ $isClient ? 'mine' : '' }}"><div class="message-bubble"><div class="message-meta">{{ $isClient ? 'Vous' : 'Commercial' }} · {{ $message->created_at?->format('d/m/Y H:i') }}</div>{{ $message->contenu }}</div></div>
                    @endforeach
                </div>

                {{-- Une réclamation résolue conserve la conversation, sans formulaire de réponse. --}}
                @if ($reclamation->statut !== 'resolue')
                    <div class="conversation-composer">
                        <form method="POST" action="{{ route('reclamations.messages.store', $reclamation->id_reclamation) }}">
                            @csrf
                            <label for="contenu" class="form-label">Ajouter un message</label>
                            <textarea id="contenu" name="contenu" rows="4" class="form-control @error('contenu') is-invalid @enderror" placeholder="Écrivez votre message..." required>{{ old('contenu') }}</textarea>
                            @error('contenu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="text-end mt-3"><button type="submit" class="btn btn-djezzy px-4"><i class="bi bi-send me-2" aria-hidden="true"></i>Envoyer</button></div>
                        </form>
                    </div>
                @else
                    <div class="conversation-composer"><div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2" aria-hidden="true"></i>Cette réclamation a été résolue.</div></div>
                @endif
            </section>
        </div>
    </div>
@endsection
