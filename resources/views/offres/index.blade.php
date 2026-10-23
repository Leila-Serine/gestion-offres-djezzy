@extends('layouts.app')

@section('title', 'Offres')
@section('breadcrumb', 'Offres')

@section('content')

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Offres</h1>
            <p class="page-subtitle mb-0">
                Consultez et filtrez les offres disponibles.
            </p>
        </div>

        @if (auth()->user()->role === 'commercial')
            <a href="{{ route('offres.create') }}" class="btn btn-danger">
                <i class="bi bi-plus-lg me-1"></i>
                Nouvelle offre
            </a>
        @endif
    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    @include('offres._filtres')


    {{-- =========================================================
         LISTE DES OFFRES
    ========================================================== --}}
    <div class="offers-card">

        <div class="table-responsive">
            <table class="table offers-table align-middle mb-0">

                <thead>
                    <tr>
                        <th class="ps-4">Offre</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Caractéristiques</th>
                        <th>Disponibilité</th>

                        @if (auth()->user()->role === 'commercial')
                            <th class="text-end pe-4">Actions</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @forelse ($offres as $offre)

                        @php
                            $stockTotal = $offre->disponibilites
                                ->sum('quantite_disponible');

                            /*
                             * On montre 3 caractéristiques par défaut.
                             */
                            $caracteristiquesVisibles =
                                $offre->caracteristiques->take(3);

                            $caracteristiquesCachees =
                                $offre->caracteristiques->slice(3);

                            $nombreCachees =
                                $caracteristiquesCachees->count();

                            $rowId = 'offer-' . $offre->id_offre;
                        @endphp


                        <tr class="{{ $offre->statut === 'suspendue' ? 'offer-row-suspended' : '' }}">

                            {{-- =================================================
                                 OFFRE
                            ================================================== --}}
                            <td class="ps-4 offer-name-cell">

                                <div class="d-flex align-items-center flex-wrap gap-2">

                                    <a
                                        href="{{ route('offres.show', $offre) }}"
                                        class="offer-name"
                                    >
                                        {{ $offre->nom_offre }}
                                    </a>

                                    @if ($offre->statut === 'suspendue')

                                        <span class="status-chip status-chip-suspended">
                                            <i class="bi bi-pause-circle"></i>
                                            Suspendue
                                        </span>

                                    @else

                                        <span class="status-chip status-chip-active">
                                            <span class="status-dot"></span>
                                            Active
                                        </span>

                                    @endif

                                </div>

                            </td>


                            {{-- =================================================
                                 CATÉGORIE
                            ================================================== --}}
                            <td>
                                <span class="category-text">
                                    {{ $offre->categorie }}
                                </span>
                            </td>


                            {{-- =================================================
                                 PRIX
                            ================================================== --}}
                            <td>
                                @if ($offre->prix !== null)

                                    <div class="price-value">
                                        {{ number_format(
                                            (float) $offre->prix,
                                            2,
                                            ',',
                                            ' '
                                        ) }}
                                        <span>DA</span>
                                    </div>

                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>


                            {{-- =================================================
                                 CARACTÉRISTIQUES
                            ================================================== --}}
                            <td class="characteristics-cell">

                                <div
                                    class="characteristics-wrapper"
                                    id="{{ $rowId }}"
                                >

                                    {{-- 3 premières caractéristiques --}}
                                    @foreach (
                                        $caracteristiquesVisibles
                                        as $caracteristique
                                    )

                                        @php
                                            $valeur =
                                                $caracteristique->valeur_numerique
                                                ?? $caracteristique->valeur;

                                            if (is_numeric($valeur)) {
                                                $valeurAffichee = rtrim(
                                                    rtrim(
                                                        number_format(
                                                            (float) $valeur,
                                                            2,
                                                            '.',
                                                            ''
                                                        ),
                                                        '0'
                                                    ),
                                                    '.'
                                                );
                                            } else {
                                                $valeurAffichee = $valeur;
                                            }
                                        @endphp

                                        <span class="characteristic-chip">

                                            <span class="characteristic-label">
                                                {{ $caracteristique->libelle }}
                                            </span>

                                            <span class="characteristic-value">
                                                {{ $valeurAffichee }}

                                                @if (!empty($caracteristique->unite))
                                                    {{ $caracteristique->unite }}
                                                @endif
                                            </span>

                                        </span>

                                    @endforeach


                                    {{-- Caractéristiques cachées --}}
                                    @foreach (
                                        $caracteristiquesCachees
                                        as $caracteristique
                                    )

                                        @php
                                            $valeur =
                                                $caracteristique->valeur_numerique
                                                ?? $caracteristique->valeur;

                                            if (is_numeric($valeur)) {
                                                $valeurAffichee = rtrim(
                                                    rtrim(
                                                        number_format(
                                                            (float) $valeur,
                                                            2,
                                                            '.',
                                                            ''
                                                        ),
                                                        '0'
                                                    ),
                                                    '.'
                                                );
                                            } else {
                                                $valeurAffichee = $valeur;
                                            }
                                        @endphp

                                        <span
                                            class="characteristic-chip extra-characteristic d-none"
                                        >

                                            <span class="characteristic-label">
                                                {{ $caracteristique->libelle }}
                                            </span>

                                            <span class="characteristic-value">
                                                {{ $valeurAffichee }}

                                                @if (!empty($caracteristique->unite))
                                                    {{ $caracteristique->unite }}
                                                @endif
                                            </span>

                                        </span>

                                    @endforeach


                                    {{-- Bouton + N autres --}}
                                    @if ($nombreCachees > 0)

                                        <button
                                            type="button"
                                            class="more-characteristics-btn"
                                            data-target="{{ $rowId }}"
                                            data-count="{{ $nombreCachees }}"
                                            aria-expanded="false"
                                        >
                                            <i class="bi bi-plus-circle"></i>

                                            <span>
                                                {{ $nombreCachees }} autres
                                            </span>
                                        </button>

                                    @endif


                                    @if ($offre->caracteristiques->isEmpty())
                                        <span class="empty-value">
                                            Aucune caractéristique
                                        </span>
                                    @endif

                                </div>

                            </td>


                            {{-- =================================================
                                 DISPONIBILITÉ
                            ================================================== --}}
                            <td>

                                @if ($stockTotal > 0)

                                    <span class="stock-chip stock-available">
                                        <span class="stock-dot"></span>
                                        {{ $stockTotal }} en stock
                                    </span>

                                @elseif (
                                    ($offre->mode_stock_zero ?? null)
                                    === 'bientot_disponible'
                                )

                                    <span class="stock-chip stock-soon">
                                        <i class="bi bi-clock"></i>
                                        Bientôt disponible
                                    </span>

                                @else

                                    <span class="stock-chip stock-empty">
                                        <i class="bi bi-dash-circle"></i>
                                        Stock nul
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 ACTIONS
                            ================================================== --}}
                            @if (auth()->user()->role === 'commercial')

                                <td class="text-end pe-4">

                                    <div class="d-flex justify-content-end gap-2">

                                        <a
                                            href="{{ route('offres.edit', $offre) }}"
                                            class="action-btn action-edit"
                                            title="Modifier"
                                        >
                                            <i class="bi bi-pencil"></i>
                                            <span>Modifier</span>
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route('offres.destroy', $offre) }}"
                                            onsubmit="
                                                return confirm(
                                                    'Voulez-vous vraiment supprimer cette offre ?'
                                                );
                                            "
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="action-btn action-delete"
                                                title="Supprimer"
                                            >
                                                <i class="bi bi-trash"></i>
                                                <span>Supprimer</span>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            @endif

                        </tr>


                    @empty

                        <tr>
                            <td
                                colspan="{{ auth()->user()->role === 'commercial' ? 6 : 5 }}"
                            >

                                <div class="empty-state">

                                    <div class="empty-state-icon">
                                        <i class="bi bi-search"></i>
                                    </div>

                                    <div class="fw-semibold">
                                        Aucune offre trouvée
                                    </div>

                                    <div class="text-muted small mt-1">
                                        Modifiez les filtres pour afficher d'autres offres.
                                    </div>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

    </div>


    {{-- =========================================================
         PAGINATION
    ========================================================== --}}
    @if (method_exists($offres, 'links'))
        <div class="mt-4">
            {{ $offres->links() }}
        </div>
    @endif

@endsection



@push('styles')

<style>

    /* =========================================================
       CONTENEUR
    ========================================================== */

    .offers-card {
        background: #ffffff;
        border: 1px solid #e7e9ee;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(20, 27, 38, 0.05);
    }


    /* =========================================================
       TABLEAU
    ========================================================== */

    .offers-table {
        font-size: 14px;
    }

    .offers-table thead th {
        padding: 17px 16px;
        background: #fafbfc;
        border-bottom: 1px solid #e5e7eb;

        color: #667085;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .035em;

        white-space: nowrap;
    }

    .offers-table tbody td {
        padding: 18px 16px;
        border-bottom: 1px solid #eef0f3;
        vertical-align: middle;
    }

    .offers-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .offers-table tbody tr {
        transition:
            background-color .15s ease,
            box-shadow .15s ease;
    }

    .offers-table tbody tr:hover {
        background: #fcfcfd;
    }


    /* =========================================================
       OFFRE
    ========================================================== */

    .offer-name-cell {
        min-width: 210px;
    }

    .offer-name {
        color: #101828;
        font-weight: 700;
        text-decoration: none;
        line-height: 1.35;
    }

    .offer-name:hover {
        color: #e30613;
    }


    /* =========================================================
       STATUT
    ========================================================== */

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 4px 8px;
        border-radius: 999px;

        font-size: 11px;
        font-weight: 700;

        white-space: nowrap;
    }

    .status-chip-active {
        background: #ecfdf3;
        color: #067647;
    }

    .status-chip-suspended {
        background: #f2f4f7;
        color: #667085;
        border: 1px solid #e4e7ec;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }


    /* Ligne suspendue légèrement atténuée */
    .offer-row-suspended {
        background: #fcfcfd;
    }

    .offer-row-suspended .offer-name {
        color: #475467;
    }


    /* =========================================================
       CATÉGORIE
    ========================================================== */

    .category-text {
        color: #475467;
        font-weight: 500;
        white-space: nowrap;
    }


    /* =========================================================
       PRIX
    ========================================================== */

    .price-value {
        color: #101828;
        font-size: 15px;
        font-weight: 700;
        white-space: nowrap;
    }

    .price-value span {
        color: #667085;
        font-size: 12px;
        font-weight: 600;
    }


    /* =========================================================
       CARACTÉRISTIQUES
    ========================================================== */

    .characteristics-cell {
        min-width: 390px;
        max-width: 520px;
    }

    .characteristics-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }

    .characteristic-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;

        padding: 6px 9px;

        border: 1px solid #e4e7ec;
        border-radius: 7px;

        background: #f9fafb;
        color: #344054;

        font-size: 12px;

        white-space: nowrap;
    }

    .characteristic-label {
        font-weight: 600;
    }

    .characteristic-value {
        color: #667085;
        font-weight: 500;
    }


    /* =========================================================
       BOUTON +N AUTRES
    ========================================================== */

    .more-characteristics-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 6px 10px;

        border: 0;
        border-radius: 999px;

        background: #667085;
        color: #ffffff;

        font-size: 12px;
        font-weight: 700;

        cursor: pointer;

        transition:
            background .15s ease,
            transform .15s ease;
    }

    .more-characteristics-btn:hover {
        background: #475467;
    }

    .more-characteristics-btn:active {
        transform: scale(.97);
    }


    /* =========================================================
       STOCK
    ========================================================== */

    .stock-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 9px;
        border-radius: 7px;

        font-size: 12px;
        font-weight: 700;

        white-space: nowrap;
    }

    .stock-available {
        background: #ecfdf3;
        color: #067647;
    }

    .stock-soon {
        background: #fffaeb;
        color: #b54708;
    }

    .stock-empty {
        background: #f2f4f7;
        color: #667085;
    }

    .stock-dot {
        width: 6px;
        height: 6px;

        background: currentColor;
        border-radius: 50%;
    }


    /* =========================================================
       ACTIONS
    ========================================================== */

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 7px 10px;

        border-radius: 8px;

        background: #ffffff;

        font-size: 12px;
        font-weight: 600;

        text-decoration: none;

        transition:
            background .15s ease,
            border-color .15s ease;
    }

    .action-edit {
        border: 1px solid #d0d5dd;
        color: #475467;
    }

    .action-edit:hover {
        background: #f9fafb;
        border-color: #98a2b3;
        color: #344054;
    }

    .action-delete {
        border: 1px solid #fda29b;
        color: #d92d20;
    }

    .action-delete:hover {
        background: #fef3f2;
        border-color: #f97066;
        color: #b42318;
    }


    /* =========================================================
       VIDE
    ========================================================== */

    .empty-value {
        color: #98a2b3;
        font-size: 13px;
    }

    .empty-state {
        padding: 55px 20px;
        text-align: center;
        color: #344054;
    }

    .empty-state-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 45px;
        height: 45px;

        margin: 0 auto 12px;

        border-radius: 50%;

        background: #f2f4f7;
        color: #667085;

        font-size: 19px;
    }


    /* =========================================================
       MOBILE
    ========================================================== */

    @media (max-width: 991px) {

        .characteristics-cell {
            min-width: 330px;
        }

        .action-btn span {
            display: none;
        }

        .action-btn {
            width: 34px;
            height: 34px;

            justify-content: center;
            padding: 0;
        }

    }

</style>

@endpush



@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.more-characteristics-btn')
            .forEach(function (button) {

                button.addEventListener('click', function () {

                    const targetId = button.dataset.target;

                    const wrapper =
                        document.getElementById(targetId);

                    if (!wrapper) {
                        return;
                    }

                    const hiddenCharacteristics =
                        wrapper.querySelectorAll(
                            '.extra-characteristic'
                        );

                    const currentlyExpanded =
                        button.getAttribute(
                            'aria-expanded'
                        ) === 'true';


                    /*
                     * Si ouvert -> fermer
                     */
                    if (currentlyExpanded) {

                        hiddenCharacteristics
                            .forEach(function (item) {

                                item.classList.add('d-none');

                            });


                        button.innerHTML = `
                            <i class="bi bi-plus-circle"></i>
                            <span>
                                ${button.dataset.count} autres
                            </span>
                        `;


                        button.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                    /*
                     * Si fermé -> ouvrir
                     */
                    else {

                        hiddenCharacteristics
                            .forEach(function (item) {

                                item.classList.remove(
                                    'd-none'
                                );

                            });


                        button.innerHTML = `
                            <i class="bi bi-chevron-up"></i>
                            <span>Réduire</span>
                        `;


                        button.setAttribute(
                            'aria-expanded',
                            'true'
                        );

                    }

                });

            });

    });

</script>

@endpush