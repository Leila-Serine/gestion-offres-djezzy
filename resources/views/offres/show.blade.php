@extends('layouts.app')
@section('title', $offre->nom_offre)
@section('breadcrumb', 'Offres / Détail')

@section('content')
    <div class="content-medium">
        <div class="page-heading">
            <div>
                <div class="page-eyebrow">Catalogue des offres</div>
                <h1 class="page-title">{{ $offre->nom_offre }}</h1>
                <p class="page-subtitle">Retrouvez les informations essentielles de cette offre.</p>
            </div>
            <div class="page-heading-actions">
                <a href="{{ route('offres.index') }}" class="btn btn-soft">
                    <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Retour aux offres
                </a>
                {{-- L'action de modification existante reste réservée au commercial. --}}
                @if (auth()->user()->role === 'commercial')
                    <a href="{{ route('offres.edit', $offre->id_offre) }}" class="btn btn-djezzy">
                        <i class="bi bi-pencil me-2" aria-hidden="true"></i>Modifier l'offre
                    </a>
                @endif
            </div>
        </div>

        <article class="app-card offer-detail-card" aria-labelledby="offerTitle">
            <div class="offer-detail-header">
                <div class="offer-heading">
                    <span class="offer-detail-icon" aria-hidden="true"><i class="bi bi-sim"></i></span>
                    <div>
                        <span class="offer-kicker">Offre Djezzy</span>
                        <h2 id="offerTitle" class="offer-detail-title">{{ $offre->nom_offre }}</h2>
                    </div>
                </div>
                <span class="category-badge">{{ $offre->categorie }}</span>
            </div>

            <div class="offer-detail-body">
                <div class="row g-4 g-lg-5">
                    <div class="col-lg-8">
                        {{-- Le prix est l'information principale pour la consultation de l'offre. --}}
                        <div class="offer-price-panel">
                            <span class="offer-price-label">Prix unitaire</span>
                            <strong class="offer-price">{{ number_format((float) $offre->prix, 2, ',', ' ') }} <span>DA</span></strong>
                        </div>

                        <dl class="offer-facts mb-0">
                            <div>
                                <dt>Référence</dt>
                                <dd>#{{ $offre->id_offre }}</dd>
                            </div>
                            <div>
                                <dt>Catégorie</dt>
                                <dd>{{ $offre->categorie }}</dd>
                            </div>
                        </dl>

                        {{-- Les caractéristiques sont affichées seulement si l'offre en possède. --}}
                        @if ($offre->caracteristiques->isNotEmpty())
                            <section class="offer-features" aria-labelledby="featuresTitle">
                                <h3 id="featuresTitle">Caractéristiques de l'offre</h3>
                                <div class="row g-3">
                                    @foreach ($offre->caracteristiques as $caracteristique)
                                        <div class="col-sm-6">
                                            <div class="offer-feature-card">
                                                <span>{{ $caracteristique->libelle }}</span>
                                                <strong>
                                                    {{ $caracteristique->valeur }}
                                                    @if ($caracteristique->unite)
                                                        {{ $caracteristique->unite }}
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        {{-- La wilaya n'est connue qu'après la sélection d'une boutique. --}}
                        <section class="regional-note" aria-labelledby="availabilityTitle">
                            <span class="regional-note-icon" aria-hidden="true"><i class="bi bi-geo-alt"></i></span>
                            <div>
                                <h3 id="availabilityTitle">Disponibilité régionale</h3>
                                <p>La disponibilité de cette offre dépend de la wilaya de la boutique sélectionnée lors de la commande.</p>
                            </div>
                        </section>

                        <p class="offer-created mb-0">
                            <i class="bi bi-calendar3 me-2" aria-hidden="true"></i>
                            Créée le {{ $offre->created_at?->timezone('Africa/Algiers')->format('d/m/Y à H:i') ?? '—' }}
                        </p>
                    </div>

                    <div class="col-lg-4">
                        <aside class="offer-action-panel" aria-label="Actions sur l'offre">
                            <span class="offer-action-icon" aria-hidden="true">
                                <i class="bi {{ auth()->user()->role === 'client' ? 'bi-bag-plus' : 'bi-pencil-square' }}"></i>
                            </span>

                            @if (auth()->user()->role === 'client')
                                <h3>Commander cette offre</h3>
                                <p>Choisissez votre boutique et indiquez les offres souhaitées dans le formulaire de commande.</p>

                                @if (Route::has('commandes.create'))
                                    <a href="{{ route('commandes.create') }}" class="btn btn-djezzy w-100">
                                        <i class="bi bi-bag-plus me-2" aria-hidden="true"></i>Passer une commande
                                    </a>
                                @endif
                            @else
                                <h3>Gestion de l'offre</h3>
                                <p>Consultez ou modifiez les informations affichées dans le catalogue.</p>
                                @if (auth()->user()->role === 'commercial')
                                    <a href="{{ route('offres.edit', $offre->id_offre) }}" class="btn btn-djezzy w-100">
                                        <i class="bi bi-pencil me-2" aria-hidden="true"></i>Modifier l'offre
                                    </a>
                                @endif
                            @endif

                            <a href="{{ route('offres.index') }}" class="btn btn-soft w-100 mt-2">Voir toutes les offres</a>
                        </aside>
                    </div>
                </div>
            </div>
        </article>
    </div>
@endsection

@push('styles')
<style>
    /* Styles propres à la fiche d'offre ; le layout garde son apparence commune. */
    .offer-detail-card{overflow:hidden}
    .offer-detail-header{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:25px 30px;border-bottom:1px solid var(--border-color)}
    .offer-heading{display:flex;align-items:center;gap:15px;min-width:0}
    .offer-detail-icon,.offer-action-icon{display:grid;flex:0 0 auto;place-items:center;width:48px;height:48px;border:1px solid #ffdade;border-radius:12px;background:var(--djezzy-red-light);color:var(--djezzy-red);font-size:21px}
    .offer-kicker{display:block;margin-bottom:3px;color:var(--text-muted);font-size:11px;font-weight:700}
    .offer-detail-title{margin:0;color:var(--text-main);font-size:18px;font-weight:800;line-height:1.3;overflow-wrap:anywhere}
    .offer-detail-body{padding:30px}
    .offer-price-panel{margin-bottom:28px;padding:24px 26px;border:1px solid #f3dade;border-radius:13px;background:linear-gradient(110deg,#fff5f6,#fff)}
    .offer-price-label{display:block;margin-bottom:4px;color:#765d63;font-size:12px;font-weight:700}
    .offer-price{display:block;color:var(--djezzy-red);font-size:clamp(28px,4vw,39px);font-weight:800;letter-spacing:-.04em;line-height:1.2;font-variant-numeric:tabular-nums}
    .offer-price span{font-size:.48em;letter-spacing:0}
    .offer-facts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
    .offer-facts>div{padding:0 0 18px;border-bottom:1px solid var(--border-color)}
    .offer-facts dt{margin-bottom:7px;color:var(--text-muted);font-size:12px;font-weight:600}
    .offer-facts dd{margin:0;color:var(--text-main);font-size:14px;font-weight:800;overflow-wrap:anywhere}
    .offer-features{margin-top:29px}.offer-features h3{margin:0 0 16px;font-size:15px;font-weight:800}
    .offer-feature-card{height:100%;padding:18px 20px;border:1px solid var(--border-color);border-radius:12px;background:#fff;box-shadow:0 4px 15px #141b2605}
    .offer-feature-card span{display:block;margin-bottom:7px;color:var(--text-muted);font-size:12px}
    .offer-feature-card strong{display:block;color:var(--text-main);font-size:15px;font-weight:800;overflow-wrap:anywhere}
    .regional-note{display:flex;align-items:flex-start;gap:14px;margin-top:29px;padding:20px;border:1px solid #e5e8ed;border-radius:12px;background:#f8f9fb}
    .regional-note-icon{display:grid;flex:0 0 auto;place-items:center;width:37px;height:37px;border-radius:10px;background:#fff;color:var(--djezzy-red);font-size:17px}
    .regional-note h3{margin:0 0 5px;font-size:14px;font-weight:800}
    .regional-note p{margin:0;color:var(--text-muted);font-size:12px;line-height:1.6}
    .offer-created{margin-top:23px;color:var(--text-muted);font-size:12px}
    .offer-action-panel{display:flex;flex-direction:column;height:100%;padding:25px;border:1px solid var(--border-color);border-radius:13px;background:#fff}
    .offer-action-panel h3{margin:20px 0 6px;font-size:15px;font-weight:800}
    .offer-action-panel p{margin:0 0 22px;color:var(--text-muted);font-size:12px;line-height:1.6}
    .offer-action-panel .btn:first-of-type{margin-top:auto}
    @media(max-width:767.98px){.offer-detail-header{align-items:flex-start;flex-direction:column;padding:21px}.offer-detail-body{padding:21px}.offer-facts{gap:14px}.offer-action-panel{padding:21px}}
    @media(max-width:400px){.offer-facts{grid-template-columns:1fr}}
</style>
@endpush
