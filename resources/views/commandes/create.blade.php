@extends('layouts.app')
@section('title', 'Nouvelle commande')
@section('breadcrumb', 'Mes commandes / Nouvelle commande')

@section('content')
    <div class="content-medium">
        <div class="page-heading">
            <div>
                <div class="page-eyebrow">Mes commandes</div>
                <h1 class="page-title">Nouvelle commande</h1>
                <p class="page-subtitle">Sélectionnez une boutique, puis composez votre commande.</p>
            </div>
            <a href="{{ route('commandes.index') }}" class="btn btn-soft">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Retour
            </a>
        </div>

        {{-- Repère visuel : le choix de la boutique détermine les disponibilités régionales. --}}
        <div class="order-intro mb-4">
            <div class="order-intro-icon" aria-hidden="true"><i class="bi bi-bag-check"></i></div>
            <div>
                <strong>Composer votre commande</strong>
                <p>Les offres affichées dépendent du stock dans la wilaya de votre boutique.</p>
            </div>
            <span class="order-intro-mark" aria-hidden="true">01 / 02</span>
        </div>

        <form method="POST" action="{{ route('commandes.store') }}">
            @csrf

            <section class="app-card mb-4" aria-labelledby="boutiqueTitle">
                <div class="section-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="order-step" aria-hidden="true">01</span>
                        <div>
                            <h2 id="boutiqueTitle" class="section-title">Choisir une boutique</h2>
                            <p class="section-subtitle">La disponibilité des offres sera calculée pour sa wilaya.</p>
                        </div>
                    </div>
                    <span class="item-icon" aria-hidden="true"><i class="bi bi-shop"></i></span>
                </div>
                <div class="section-body">
                    <label for="id_boutique" class="form-label">Boutique concernée</label>
                    <select id="id_boutique" name="id_boutique" class="form-select @error('id_boutique') is-invalid @enderror" required>
                        <option value="">Sélectionner une boutique</option>
                        @foreach ($boutiques as $boutique)
                            <option value="{{ $boutique->id_boutique }}" data-wilaya="{{ $boutique->wilaya }}" @selected(old('id_boutique') == $boutique->id_boutique)>
                                {{ $boutique->nom_boutique }} — {{ $boutique->wilaya }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_boutique')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text mt-2"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Choisissez d'abord la boutique pour voir les offres disponibles.</div>
                </div>
            </section>

            <section class="app-card" aria-labelledby="offersTitle">
                <div class="section-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="order-step" aria-hidden="true">02</span>
                        <div>
                            <h2 id="offersTitle" class="section-title">Choisir les offres</h2>
                            <p class="section-subtitle">Cochez les offres souhaitées et précisez chaque quantité.</p>
                        </div>
                    </div>
                    <span class="item-icon" aria-hidden="true"><i class="bi bi-sim"></i></span>
                </div>
                <div class="section-body">
                    <div class="availability-context mb-3" role="status" aria-live="polite">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <span id="availabilityContext">Sélectionnez une boutique pour consulter la disponibilité.</span>
                    </div>

                    {{-- Chaque ligne garde les noms de champs attendus par commandes.store. --}}
                    <div class="offer-list">
                        @forelse ($offres as $index => $offre)
                            @php
                                $lignePrecedente = collect(old('lignes', []))->firstWhere('id_offre', $offre->id_offre);
                            @endphp
                            <div class="select-offer offre-commande awaiting-boutique"
                                 data-offre-id="{{ $offre->id_offre }}"
                                 data-mode-stock-zero="{{ $offre->mode_stock_zero }}">
                                <div class="offer-choice">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="checkbox"
                                               id="offre_{{ $offre->id_offre }}"
                                               onchange="toggleQuantite({{ $offre->id_offre }})"
                                               @checked($lignePrecedente !== null) disabled>
                                    </div>
                                    <label class="offer-identity" for="offre_{{ $offre->id_offre }}">
                                        <span class="offer-symbol" aria-hidden="true"><i class="bi bi-sim"></i></span>
                                        <span>
                                            <strong class="offer-name">{{ $offre->nom_offre }}</strong>
                                            <span class="offer-category">{{ $offre->categorie }}</span>
                                            <span class="offer-price">{{ number_format((float) $offre->prix, 2, ',', ' ') }} DA</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="offer-controls">
                                    <div class="offer-availability">
                                        <span class="stock-available d-none"><i class="bi bi-check-circle me-1" aria-hidden="true"></i><span class="stock-available-text"></span></span>
                                        <span class="stock-zero-message d-none"><i class="bi bi-clock me-1" aria-hidden="true"></i>Bientôt disponible</span>
                                    </div>
                                    <input type="hidden" name="lignes[{{ $index }}][id_offre]"
                                           value="{{ $offre->id_offre }}" disabled id="offre_hidden_{{ $offre->id_offre }}">
                                    <div class="quantity-field">
                                        <label for="quantite_{{ $offre->id_offre }}" class="form-label mb-1">Quantité</label>
                                        <input type="number" min="1" step="1"
                                               name="lignes[{{ $index }}][quantite]"
                                               id="quantite_{{ $offre->id_offre }}"
                                               class="form-control quantite-offre"
                                               value="{{ $lignePrecedente['quantite'] ?? '' }}" required disabled>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-4">
                                <div class="empty-icon"><i class="bi bi-inboxes" aria-hidden="true"></i></div>
                                <h3>Aucune offre disponible</h3>
                                <p>Les offres apparaîtront ici lorsqu'elles seront proposées.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($offres->isNotEmpty())
                        <div id="noOffersInRegion" class="empty-region d-none" role="status">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <div><strong>Aucune offre affichée dans cette wilaya</strong><span>Vous pouvez choisir une autre boutique.</span></div>
                        </div>
                    @endif

                    @error('lignes')<div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>@enderror
                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-djezzy px-4"><i class="bi bi-check2 me-2" aria-hidden="true"></i>Passer la commande</button>
                        <a href="{{ route('commandes.index') }}" class="btn btn-soft px-4">Annuler</a>
                    </div>
                </div>
            </section>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .order-intro{display:flex;align-items:center;gap:16px;padding:21px 24px;border:1px solid #f3d8db;border-radius:15px;background:linear-gradient(105deg,#fff6f7 0%,#fff 80%)}
    .order-intro-icon{display:grid;flex:0 0 48px;place-items:center;width:48px;height:48px;border-radius:12px;background:var(--djezzy-red);color:#fff;font-size:22px}
    .order-intro strong{display:block;color:var(--text-main);font-size:15px}.order-intro p{margin:4px 0 0;color:var(--text-muted);font-size:13px}
    .order-intro-mark{margin-left:auto;color:#bd9da2;font-size:12px;font-weight:800;white-space:nowrap}
    .order-step{display:grid;flex:0 0 38px;place-items:center;width:38px;height:38px;border:1px solid #f5d6d9;border-radius:10px;background:var(--djezzy-red-light);color:var(--djezzy-red);font-size:12px;font-weight:800}
    .availability-context{display:flex;align-items:center;gap:9px;padding:11px 14px;border:1px solid #e7e9ee;border-radius:9px;background:#f8f9fb;color:#5f6976;font-size:12px;font-weight:600}
    .availability-context .bi{color:var(--djezzy-red);font-size:16px}
    .offer-list{display:grid;gap:12px}.select-offer{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 20px;border:1px solid var(--border-color);border-radius:12px;background:#fff;transition:border-color .16s,background .16s,opacity .16s}
    .select-offer:has(.form-check-input:checked){border-color:#ecabb1;background:#fffafa}.select-offer.awaiting-boutique{background:#fafbfc}
    .offer-choice{display:flex;align-items:center;gap:15px;min-width:0}.offer-choice .form-check-input{float:none;margin:0;width:18px;height:18px}
    .offer-identity{display:flex;align-items:center;gap:12px;min-width:0;cursor:pointer}.offer-symbol{display:grid;flex:0 0 40px;place-items:center;width:40px;height:40px;border-radius:10px;background:#f6f7f9;color:#697381;font-size:18px}
    .offer-name{display:block;overflow-wrap:anywhere;color:var(--text-main);font-size:14px;font-weight:750}.offer-category{display:block;margin-top:3px;color:var(--text-muted);font-size:12px}.offer-price{display:block;margin-top:6px;color:var(--djezzy-red);font-size:12px;font-weight:800}
    .offer-controls{display:flex;align-items:center;justify-content:flex-end;gap:18px;flex:0 0 auto}.offer-availability{min-width:145px;text-align:right;font-size:12px;font-weight:700}
    .stock-available{color:#23734c}.stock-zero-message{color:#83602e}.quantity-field{width:130px}.quantity-field .form-label{color:#68717e;font-size:11px}.quantity-field .form-control{min-height:39px}
    .empty-region{display:flex;align-items:center;gap:12px;margin-top:12px;padding:18px;border:1px dashed #d8dce2;border-radius:11px;background:#fafbfc;color:#606b78}
    .empty-region>.bi{font-size:22px}.empty-region strong,.empty-region span{display:block;font-size:12px}.empty-region span{margin-top:2px;color:var(--text-muted)}
    @media(max-width:767.98px){.select-offer{align-items:stretch;flex-direction:column;gap:14px}.offer-controls{justify-content:space-between;flex-wrap:wrap;gap:12px;padding-left:33px}.offer-availability{min-width:0;text-align:left}.quantity-field{width:115px}.order-intro-mark{display:none}}
    @media(max-width:400px){.offer-controls{padding-left:0}}
</style>
@endpush

@php
    /* Données minimales nécessaires à l'affichage du stock, sans nouvelle route ni appel réseau. */
    $donneesStock = $offres->map(function ($offre) {
        return [
            'id_offre' => $offre->id_offre,
            'mode_stock_zero' => $offre->mode_stock_zero,
            'disponibilites' => $offre->disponibilites->map(function ($stock) {
                return ['zone' => $stock->zone, 'quantite' => $stock->quantite_disponible];
            })->values(),
        ];
    })->values();
@endphp
@push('scripts')
<script>
    // Les contrôles visuels facilitent la saisie ; le serveur reste responsable de valider la commande.
    const offres = @json($donneesStock);
    const boutiqueSelect = document.getElementById('id_boutique');
    const regionEmpty = document.getElementById('noOffersInRegion');
    const availabilityContext = document.getElementById('availabilityContext');

    function toggleQuantite(idOffre) {
        const checkbox = document.getElementById('offre_' + idOffre);
        const quantite = document.getElementById('quantite_' + idOffre);
        const hidden = document.getElementById('offre_hidden_' + idOffre);
        const selectionActive = checkbox.checked && !checkbox.disabled;
        quantite.disabled = !selectionActive;
        hidden.disabled = !selectionActive;
        if (!selectionActive) quantite.value = '';
    }

    function mettreAJourDisponibilites() {
        const option = boutiqueSelect.options[boutiqueSelect.selectedIndex];
        const wilaya = option?.dataset.wilaya?.trim() || '';
        let offresVisibles = 0;

        document.querySelectorAll('.offre-commande').forEach(function (element) {
            const idOffre = Number(element.dataset.offreId);
            const offre = offres.find(item => Number(item.id_offre) === idOffre);
            const checkbox = element.querySelector('.form-check-input');
            const quantite = element.querySelector('.quantite-offre');
            const stockDisponible = element.querySelector('.stock-available');
            const stockTexte = element.querySelector('.stock-available-text');
            const message = element.querySelector('.stock-zero-message');
            if (!offre) return;

            // Aucune wilaya n'est déterminée tant que la boutique n'est pas choisie.
            element.classList.toggle('awaiting-boutique', !wilaya);
            if (!wilaya) {
                element.classList.remove('d-none');
                stockDisponible.classList.add('d-none');
                message.classList.add('d-none');
                checkbox.disabled = true;
                checkbox.checked = false;
                toggleQuantite(idOffre);
                return;
            }

            const disponibilite = offre.disponibilites.find(stock => String(stock.zone).trim() === wilaya);
            const quantiteDisponible = disponibilite ? Number(disponibilite.quantite) : 0;

            if (quantiteDisponible > 0) {
                offresVisibles++;
                element.classList.remove('d-none');
                checkbox.disabled = false;
                quantite.max = String(quantiteDisponible);
                stockTexte.textContent = 'Disponible · ' + quantiteDisponible;
                stockDisponible.classList.remove('d-none');
                message.classList.add('d-none');
                // Une quantité supérieure au nouveau stock doit être renseignée à nouveau.
                if (quantite.value && Number(quantite.value) > quantiteDisponible) quantite.value = '';
                toggleQuantite(idOffre);
                return;
            }

            checkbox.checked = false;
            checkbox.disabled = true;
            toggleQuantite(idOffre);
            stockDisponible.classList.add('d-none');
            const masquee = offre.mode_stock_zero === 'masquee';
            element.classList.toggle('d-none', masquee);
            message.classList.toggle('d-none', masquee);
            if (!masquee) offresVisibles++;
        });

        availabilityContext.textContent = wilaya
            ? 'Disponibilité des offres pour la wilaya : ' + wilaya
            : 'Sélectionnez une boutique pour consulter la disponibilité.';
        if (regionEmpty) regionEmpty.classList.toggle('d-none', !wilaya || offresVisibles > 0);
    }

    boutiqueSelect.addEventListener('change', mettreAJourDisponibilites);
    mettreAJourDisponibilites();
</script>
@endpush
