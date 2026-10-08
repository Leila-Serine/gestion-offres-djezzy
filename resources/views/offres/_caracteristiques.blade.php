@php
    // Après une erreur de validation, old() restaure exactement les lignes saisies.
    // En modification, les caractéristiques enregistrées servent de valeurs initiales.
    $caracteristiquesForm = old(
        'caracteristiques',
        isset($offre)
            ? $offre->caracteristiques->map(fn ($caracteristique) => [
                'libelle' => $caracteristique->libelle,
                'valeur' => $caracteristique->valeur,
                'unite' => $caracteristique->unite,
            ])->values()->toArray()
            : [['libelle' => '', 'valeur' => '', 'unite' => '']]
    );

    // Ne jamais réutiliser un indice après suppression d'une ligne ou retour de validation.
    $prochainIndex = count($caracteristiquesForm)
        ? max(array_map('intval', array_keys($caracteristiquesForm))) + 1
        : 0;
@endphp

<div class="characteristics-field mb-4" aria-labelledby="characteristicsTitle">
    <div class="characteristics-heading">
        <div>
            <h3 id="characteristicsTitle" class="characteristics-title">Caractéristiques de l'offre</h3>
            <p class="characteristics-caption">Internet, appels, SMS, validité, crédit, etc.</p>
        </div>
        <button type="button" class="btn btn-soft btn-sm" id="ajouterCaracteristique">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Ajouter
        </button>
    </div>

    @error('caracteristiques')
        <div class="alert alert-danger py-2 mb-3" role="alert">{{ $message }}</div>
    @enderror

    <div id="caracteristiquesContainer" data-next-index="{{ $prochainIndex }}">
        @foreach ($caracteristiquesForm as $index => $caracteristique)
            <div class="caracteristique-row">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="caracteristique_libelle_{{ $index }}" class="form-label">Caractéristique</label>
                        <input type="text" id="caracteristique_libelle_{{ $index }}"
                               name="caracteristiques[{{ $index }}][libelle]"
                               value="{{ $caracteristique['libelle'] ?? '' }}" placeholder="Ex. Internet"
                               class="form-control @error('caracteristiques.'.$index.'.libelle') is-invalid @enderror">
                        @error('caracteristiques.'.$index.'.libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="caracteristique_valeur_{{ $index }}" class="form-label">Valeur</label>
                        <input type="text" id="caracteristique_valeur_{{ $index }}"
                               name="caracteristiques[{{ $index }}][valeur]"
                               value="{{ $caracteristique['valeur'] ?? '' }}" placeholder="Ex. 200"
                               class="form-control @error('caracteristiques.'.$index.'.valeur') is-invalid @enderror">
                        @error('caracteristiques.'.$index.'.valeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="caracteristique_unite_{{ $index }}" class="form-label">Unité</label>
                        <input type="text" id="caracteristique_unite_{{ $index }}"
                               name="caracteristiques[{{ $index }}][unite]"
                               value="{{ $caracteristique['unite'] ?? '' }}" placeholder="Ex. Go"
                               class="form-control @error('caracteristiques.'.$index.'.unite') is-invalid @enderror">
                        @error('caracteristiques.'.$index.'.unite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-soft text-danger w-100 supprimer-caracteristique"
                                aria-label="Supprimer cette caractéristique" title="Supprimer cette caractéristique">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Les noms des champs générés gardent la structure caracteristiques[index][attribut]. --}}
<template id="caracteristiqueTemplate">
    <div class="caracteristique-row">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="caracteristique_libelle___INDEX__" class="form-label">Caractéristique</label>
                <input type="text" id="caracteristique_libelle___INDEX__"
                       class="form-control" name="caracteristiques[__INDEX__][libelle]" placeholder="Ex. Internet">
            </div>
            <div class="col-md-4">
                <label for="caracteristique_valeur___INDEX__" class="form-label">Valeur</label>
                <input type="text" id="caracteristique_valeur___INDEX__"
                       class="form-control" name="caracteristiques[__INDEX__][valeur]" placeholder="Ex. 200">
            </div>
            <div class="col-md-3">
                <label for="caracteristique_unite___INDEX__" class="form-label">Unité</label>
                <input type="text" id="caracteristique_unite___INDEX__"
                       class="form-control" name="caracteristiques[__INDEX__][unite]" placeholder="Ex. Go">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-soft text-danger w-100 supprimer-caracteristique"
                        aria-label="Supprimer cette caractéristique" title="Supprimer cette caractéristique">
                    <i class="bi bi-trash" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</template>

@push('styles')
<style>
    .characteristics-field{padding:20px;border:1px solid var(--border-color);border-radius:13px;background:#fafbfc}
    .characteristics-heading{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:16px}
    .characteristics-title{margin:0;font-size:14px;font-weight:800}
    .characteristics-caption{margin:4px 0 0;color:var(--text-muted);font-size:12px}
    .caracteristique-row{padding:17px;margin-bottom:11px;border:1px solid #e3e6ea;border-radius:11px;background:#fff}
    .caracteristique-row:last-child{margin-bottom:0}
    .caracteristique-row .form-label{margin-bottom:6px;font-size:11px}
    .caracteristique-row .supprimer-caracteristique{min-height:44px}
    @media(max-width:575.98px){.characteristics-heading{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('caracteristiquesContainer');
        const bouton = document.getElementById('ajouterCaracteristique');
        const template = document.getElementById('caracteristiqueTemplate');
        if (!container || !bouton || !template) return;

        let index = Number(container.dataset.nextIndex);

        bouton.addEventListener('click', function () {
            // Un indice unique évite que deux lignes écrasent la même valeur lors de l'envoi.
            const html = template.innerHTML.replaceAll('__INDEX__', String(index++));
            container.insertAdjacentHTML('beforeend', html);
            container.lastElementChild?.querySelector('input')?.focus();
        });

        container.addEventListener('click', function (event) {
            const suppression = event.target.closest('.supprimer-caracteristique');
            if (!suppression || !container.contains(suppression)) return;
            suppression.closest('.caracteristique-row')?.remove();
            bouton.focus();
        });
    });
</script>
@endpush
