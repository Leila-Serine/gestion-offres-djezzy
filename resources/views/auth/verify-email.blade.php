@extends('layouts.guest')
@section('title', 'Vérification de l’adresse e-mail')
@section('content')
    <div class="item-icon mb-4" aria-hidden="true"><i class="bi bi-envelope-check"></i></div><div class="page-eyebrow">Sécurité du compte</div><h1 class="mb-3">Vérifiez votre adresse e-mail</h1>
    <p class="lead-copy">Un lien de validation a été envoyé à :</p><p class="fw-bold text-break">{{ auth()->user()->email }}</p><p class="lead-copy mb-4">Cliquez sur le lien reçu pour activer votre compte.</p>
    @if (session('status') === 'verification-link-sent')<div class="alert alert-success" role="status">Un nouveau lien de vérification a été envoyé.</div>@endif
    {{-- Deux formulaires distincts : renvoyer le lien ou se déconnecter. --}}
    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">@csrf<button type="submit" class="btn btn-djezzy w-100"><i class="bi bi-send me-2" aria-hidden="true"></i>Renvoyer le lien</button></form>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-soft w-100">Se déconnecter</button></form>
@endsection
