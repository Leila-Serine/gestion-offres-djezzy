@extends('layouts.app')
@section('title', 'Modifier une offre')
@section('breadcrumb', 'Offres / Modifier')
@section('content')
    <div class="content-narrow">
        <div class="page-heading"><div><div class="page-eyebrow">Gestion des offres</div><h1 class="page-title">Modifier une offre</h1><p class="page-subtitle">Modifiez les informations de l'offre.</p></div><a href="{{ route('offres.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Retour aux offres</a></div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><p class="fw-semibold mb-2">Veuillez corriger les erreurs suivantes :</p><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <section class="app-card" aria-labelledby="formTitle">
            <div class="section-header"><div><h2 class="section-title" id="formTitle">Informations de l'offre</h2><p class="section-subtitle">Référence #{{ $offre->id_offre }} · Tarif et caractéristiques</p></div><span class="item-icon" aria-hidden="true"><i class="bi bi-pencil-square"></i></span></div>
            <div class="section-body">
                <form action="{{ route('offres.update', $offre->id_offre) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4"><label for="nom_offre" class="form-label">Nom de l'offre</label><input type="text" class="form-control @error('nom_offre') is-invalid @enderror" id="nom_offre" name="nom_offre" value="{{ old('nom_offre', $offre->nom_offre) }}" maxlength="100" required>@error('nom_offre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    {{-- Une catégorie historique reste sélectionnable pour éviter sa perte à l'édition. --}}
                    @php($categorieSelectionnee = old('categorie', $offre->categorie))
                    <div class="mb-4">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <select id="categorie" name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                            <option value="">Sélectionner une catégorie</option>
                            @if ($categorieSelectionnee !== '' && !in_array($categorieSelectionnee, ['Prépayée', 'Postpayée'], true))
                                <option value="{{ $categorieSelectionnee }}" selected>{{ $categorieSelectionnee }} (catégorie actuelle)</option>
                            @endif
                            <option value="Prépayée" @selected($categorieSelectionnee === 'Prépayée')>Prépayée</option>
                            <option value="Postpayée" @selected($categorieSelectionnee === 'Postpayée')>Postpayée</option>
                        </select>
                        @error('categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- old() préserve la saisie si la validation du formulaire échoue. --}}
                    <div class="mb-4">
                        <label for="prix" class="form-label">Prix unitaire</label>
                        <div class="input-group has-validation">
                            <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix', $offre->prix) }}" min="0" step="0.01" inputmode="decimal" required>
                            <span class="input-group-text">DA</span>
                            @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    {{-- old() est géré dans le partiel et préserve les lignes après validation. --}}
                    @include('offres._caracteristiques')
                    {{-- old() prévaut après une erreur ; sinon l'option sauvegardée reste cochée. --}}
                    <fieldset class="stock-display-field mb-4" @error('mode_stock_zero') aria-describedby="stockModeError" @enderror>
                        <legend class="form-label mb-1"><i class="bi bi-eye me-2" aria-hidden="true"></i>Lorsque le stock est épuisé</legend>
                        <p class="form-text mb-3">Ce choix s'applique dans la wilaya de la boutique sélectionnée par le client.</p>
                        <div class="stock-options">
                            <label class="stock-option">
                                <input class="form-check-input" type="radio" name="mode_stock_zero" value="bientot_disponible"
                                       @checked(old('mode_stock_zero', $offre->mode_stock_zero) === 'bientot_disponible') required>
                                <span>
                                    <strong><i class="bi bi-clock me-2" aria-hidden="true"></i>Afficher « Bientôt disponible »</strong>
                                    <small>L'offre reste visible, grisée et indisponible à la commande.</small>
                                </span>
                            </label>
                            <label class="stock-option">
                                <input class="form-check-input" type="radio" name="mode_stock_zero" value="masquee"
                                       @checked(old('mode_stock_zero', $offre->mode_stock_zero) === 'masquee') required>
                                <span>
                                    <strong><i class="bi bi-eye-slash me-2" aria-hidden="true"></i>Masquer l'offre</strong>
                                    <small>L'offre n'apparaît plus pour cette boutique lorsque son stock atteint zéro.</small>
                                </span>
                            </label>
                        </div>
                        @error('mode_stock_zero')<div id="stockModeError" class="text-danger small mt-2" role="alert">{{ $message }}</div>@enderror
                    </fieldset>
                    <div class="form-actions"><button type="submit" class="btn btn-djezzy px-4"><i class="bi bi-check2 me-2" aria-hidden="true"></i>Enregistrer les modifications</button><a href="{{ route('offres.index') }}" class="btn btn-soft px-4">Annuler</a></div>
                </form>
            </div>
        </section>
    </div>
@endsection
@push('styles')
<style>
    .stock-display-field{min-width:0;padding:20px;border:1px solid var(--border-color);border-radius:13px;background:#fafbfc}
    .stock-display-field legend{float:none;width:auto;padding:0;font-size:13px;font-weight:700}
    .stock-display-field legend .bi{color:var(--djezzy-red)}
    .stock-options{display:grid;gap:10px}
    .stock-option{display:flex;align-items:flex-start;gap:13px;padding:16px 18px;border:1px solid #e1e4e9;border-radius:11px;background:#fff;cursor:pointer;transition:border-color .16s,background .16s,box-shadow .16s}
    .stock-option:hover{border-color:#efb9be;background:#fffafa}
    .stock-option:has(input:checked){border-color:var(--djezzy-red);background:#fff7f8;box-shadow:0 0 0 2px #e3061312}
    .stock-option:focus-within{outline:2px solid #e3061360;outline-offset:2px}
    .stock-option .form-check-input{flex:0 0 auto;margin:3px 0 0}
    .stock-option strong{display:block;color:var(--text-main);font-size:13px;line-height:1.4}
    .stock-option:has(input:checked) strong,.stock-option:has(input:checked) strong .bi{color:var(--djezzy-red)}
    .stock-option small{display:block;margin-top:5px;color:var(--text-muted);font-size:12px;line-height:1.5}
</style>
@endpush
